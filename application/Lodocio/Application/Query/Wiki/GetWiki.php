<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Application\Query\Wiki;

use App\Linear\Application\Query\GetIssues;
use App\Linear\Application\Query\LinearConfig;
use App\Linear\Application\Query\Readmodel\IssueCacheReadModelCollection;
use App\Lodocio\Application\Command\Wiki\AddWiki\AddWiki;
use App\Lodocio\Application\Command\Wiki\AddWiki\AddWikiHandler;
use App\Lodocio\Application\Helper\CamelConverter;
use App\Lodocio\Application\Helper\SimpleImage;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiFlattenedReadModel;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiNodeFileReadModel;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiNodeGroupReadModel;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiNodeReadModel;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiReadModel;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiReadModelCollection;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiReadModelFactory;
use App\Lodocio\Domain\Model\Project\DocProjectRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeGroupRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatusRepository;
use App\Lodocio\Domain\Model\Wiki\WikiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Twig\Environment;

class GetWiki
{
    public function __construct(
        protected DocProjectRepository     $docProjectRepository,
        protected WikiRepository           $wikiRepository,
        protected WikiNodeRepository       $wikiNodeRepository,
        protected WikiNodeGroupRepository  $wikiNodeGroupRepository,
        protected WikiNodeStatusRepository $wikiNodeStatusRepository,
        protected LinearConfig             $linearConfig,
        protected Environment              $twig,
        protected string                   $uploadFolder,
        protected EntityManagerInterface   $entityManager,
    ) {
    }

    public function ByDocProject(int $docProjectId): WikiReadModelCollection
    {
        $collection = new WikiReadModelCollection();
        $project = $this->docProjectRepository->getById($docProjectId);

        $wikis = $this->wikiRepository->getByProject($project);

        // -- create a default wiki for this project
        if (count($wikis) === 0) {
            $newWiki = AddWiki::make(
                'WIKI',
                'Wiki',
                'cccccc',
                $docProjectId
            );
            $newWikiHandler = new AddWikiHandler(
                $this->entityManager,
                $this->docProjectRepository,
                $this->wikiRepository,
                $this->wikiNodeStatusRepository,
                $this->wikiNodeRepository,
                $this->wikiNodeGroupRepository,
            );
            $newWikiHandler->go($newWiki);
            $this->entityManager->flush();
            $this->entityManager->clear();

            $wikis = $this->wikiRepository->getByProject($project);
        }
        foreach ($wikis as $wiki) {
            $collection->addItem(WikiReadModel::hydrateFromModel($wiki));
        }
        return $collection;
    }

    public function ById(int $id): WikiReadModel
    {
        $wikiReadModel = WikiReadModel::hydrateFromModel($this->wikiRepository->getById($id));
        foreach ($wikiReadModel->getWikiNodeStatusReadModelCollection()->getCollection() as $status) {
            $status->setUsages($this->wikiNodeRepository->countByStatus($status->getId()));
        }
        return $wikiReadModel;
    }

    public function BySlug(string $slug): WikiReadModel
    {
        return WikiReadModel::hydrateFromModel($this->wikiRepository->getBySlug($slug));
    }

    public function FullById(int $id): WikiReadModel
    {
        $wiki = $this->wikiRepository->getById($id);
        $wikiReadModelFactory = new WikiReadModelFactory($wiki);
        return $wikiReadModelFactory->getCompleteReadModel();
    }

    public function FullBySlug(string $slug): WikiFlattenedReadModel
    {
        $wiki = $this->wikiRepository->getBySlug($slug);
        $wikiReadModelFactory = new WikiReadModelFactory($wiki);
        return $wikiReadModelFactory->getFlattenedReadModel();
    }

    // -- get list of issues of the related teams ----------------------------------------

    /**
     * @throws \Exception
     */
    public function IssuesById(int $id): IssueCacheReadModelCollection
    {
        $wiki = $this->wikiRepository->getById($id);
        $collection = new IssueCacheReadModelCollection();

        if (strlen($wiki->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
            $this->linearConfig->setKey($wiki->getProject()->getOrganisation()->getLinearApiKey());
            $getIssues = new GetIssues($this->linearConfig);
            $collection = $getIssues->ByTeams($wiki->getRelatedTeams());
        }

        return $collection;
    }

    // -- render the wiki as pdf -------------------------------------------------------

    public function renderWikiPdf(int $id): WikiReadModel
    {
        $wiki = $this->wikiRepository->getById($id);
        $wikiFactory = new WikiReadModelFactory($wiki);
        $wikiFlattened = $wikiFactory->getFlattenedReadModel();

        // ------------------------------------------------------------ convert the flattened wiki to a render object
        $renderWiki = new \stdClass();
        $renderWiki->name = $wiki->getName();
        $renderWiki->code = $wiki->getProject()->getCode() . '-' . $wiki->getCode();
        $renderWiki->uuid = $wiki->getUuid()->toRfc4122();
        $renderWiki->description = $wiki->getDescription();
        $renderWiki->projectDescription = $wiki->getProject()->getProject()->getDescription();
        $renderWiki->generatedOn = new \DateTimeImmutable();
        $renderWiki->version = 'HEAD';
        $renderWiki->elements = [];

        // -- project & logo
        $renderWiki->project = $wiki->getProject()->getName();
        if ($wiki->getProject()->getProject()->getLogo() !== '' && file_exists($this->uploadFolder . $wiki->getProject()->getProject()->getLogo())) {
            $renderWiki->logo = base64_encode(file_get_contents($this->uploadFolder . $wiki->getProject()->getProject()->getLogo()));
        } else {
            $renderWiki->logo = '';
        }

        foreach ($wikiFlattened->getElements() as $element) {
            $renderElement = new \stdClass();
            if ($element instanceof WikiNodeReadModel) {
                $renderElement->number = $element->getNumber();
                $renderElement->code = '[' . $wiki->getCode() . '-' . $element->getArtefactId() . ']';
                $renderElement->status = $element->getWikiNodeStatusReadModel()->getName();
                $renderElement->uuid = $element->getUuid();
                $renderElement->statusColor = '#' . $element->getWikiNodeStatusReadModel()->getColor();
                $renderElement->rawTitle = $element->getName();
                $renderElement->title = $this->renderTitle($element->getName(), $element->getNumber());
                $renderElement->tocTitle = str_pad($element->getNumber(), 25 + ($element->getLevel() * 3), '.', STR_PAD_RIGHT) . ' ' . $element->getName();
                $renderElement->content = $this->filterContent($element->getDescription());
                $renderElement->type = 'element';
                $renderElement->files = [];

                if (count($element->getFileReadModelCollection()->getCollection()) !== 0) {
                    /** @var WikiNodeFileReadModel $file */
                    foreach ($element->getFileReadModelCollection()->getCollection() as $wikiFile) {
                        $file = new \stdClass();
                        $file->artefactId = $wikiFile->getArtefactId();
                        $file->name = $wikiFile->getName();

                        // -- resize image if to big (max with 1800px)
                        $baseFile = $this->uploadFolder . $wikiFile->getSrcPath();
                        $resizedFile = $this->resizeImage($baseFile);
                        $file->data = base64_encode(file_get_contents($resizedFile));

                        $renderElement->files[] = $file;
                    }
                }

            } elseif ($element instanceof WikiNodeGroupReadModel) {
                $renderElement->number = $element->getNumber();
                $renderElement->code = '';
                $renderElement->status = '';
                $renderElement->statusColor = '';
                $renderElement->uuid = $element->getUuid();
                $renderElement->rawTitle = $element->getName();
                $renderElement->title = $this->renderTitle($element->getName(), $element->getNumber());
                $renderElement->tocTitle = str_pad($element->getNumber(), 25 + ($element->getLevel() * 3), '.', STR_PAD_RIGHT) . ' <b>' . $element->getName() . '</b>';
                $renderElement->content = '';
                $renderElement->type = 'group';
                $renderElement->files = [];
            }
            $renderWiki->elements[] = $renderElement;
        }

        $pdfHtml = $this->twig->render('@lodocio/Pdf/wiki-pdf-default.html.twig', ['wiki' => $renderWiki]);

        // echo $pdfHtml;
        // exit;

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($pdfHtml);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $slugger = new AsciiSlugger();
        $fileName = strtolower((string)$slugger->slug(trim(CamelConverter::CamelToKebab($wiki->getName()))));
        $fileName = $renderWiki->generatedOn->format('Ymd') . '_' . $renderWiki->code . '_' . $fileName . '_' . $renderWiki->version . '.pdf';

        $dompdf->stream($fileName, array("Attachment" => false));
        exit();

        return WikiReadModel::hydrateFromModel($wiki);
    }

    private function renderTitle($title, $number): string
    {
        $renderTitle = '';
        switch (strlen(trim($number))) {
            case 1:
                $renderTitle = '<div class="page_break"></div><h1>' . $number . '. ' . $title . '</h1>';
                break;
            case 3:
                $renderTitle = '<h2>' . $number . '. ' . $title . '</h2>';
                break;
            case 5:
                $renderTitle = '<h3>' . $number . '. ' . $title . '</h3>';
                break;
            case 7:
                $renderTitle = '<h4>' . $number . '. ' . $title . '</h4>';
                break;
            case 9:
                $renderTitle = '<h5>' . $number . '. ' . $title . '</h5>';
                break;
            default:
                $renderTitle = '<h6>' . $number . '. ' . $title . '</h6>';
                break;
        }
        $renderTitle = str_replace('->', '→', $renderTitle);
        return $renderTitle;
    }

    private function filterContent($content): string
    {
        $filteredContent = $content;
        $filteredContent = str_replace('<li><p>', '<li>', $filteredContent);
        $filteredContent = str_replace('</p></li>', '</li>', $filteredContent);
        $filteredContent = preg_replace('/colwidth="(\d+)"/', 'style="width:$1px"', $filteredContent);
        $filteredContent = str_replace('->', '→', $filteredContent);
        $filteredContent1 = nl2br($filteredContent);
        $filteredContent2 = preg_replace_callback('/<code>(.*?)<\/code>/s', function ($match) {
            return '<code>' . str_replace(' ', '&nbsp;', $match[1]) . '</code>';
        }, $filteredContent1);

        return $filteredContent2;
    }

    private function resizeImage(string $baseFile): string
    {
        $dimensions = getimagesize($baseFile);
        if ($dimensions[0] > 1800) {
            $extension = pathinfo($baseFile, PATHINFO_EXTENSION);
            $resultFile = str_replace('.' . $extension, '_1800.' . $extension, $baseFile);
            // if (false === file_exists($resultFile)) {
            $image = SimpleImage::load($baseFile);
            $image->resizeToWidth(1800);
            $image->save($resultFile);
            // }
        } else {
            $resultFile = $baseFile;
        }
        return $resultFile;
    }
}
