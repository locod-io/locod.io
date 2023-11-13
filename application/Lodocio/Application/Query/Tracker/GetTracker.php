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

namespace App\Lodocio\Application\Query\Tracker;

use App\Linear\Application\Query\GetIssues;
use App\Linear\Application\Query\LinearConfig;
use App\Linear\Application\Query\Readmodel\IssueCacheReadModelCollection;
use App\Lodocio\Application\Helper\CamelConverter;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerNodeFileReadModel;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerNodeGroupReadModel;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerNodeReadModel;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerReadModel;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerReadModelCollection;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerReadModelFactory;
use App\Lodocio\Domain\Model\Project\DocProjectRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Twig\Environment;

class GetTracker
{
    public function __construct(
        protected DocProjectRepository $docProjectRepository,
        protected TrackerRepository    $trackerRepository,
        protected LinearConfig         $linearConfig,
        protected Environment          $twig,
        protected string               $uploadFolder,
    ) {
    }

    public function ByDocProject(int $docProjectId): TrackerReadModelCollection
    {
        $collection = new TrackerReadModelCollection();
        $project = $this->docProjectRepository->getById($docProjectId);
        $trackers = $this->trackerRepository->getDocProject($project);
        foreach ($trackers as $tracker) {
            $collection->addItem(TrackerReadModel::hydrateFromModel($tracker));
        }
        return $collection;
    }

    public function ById(int $id): TrackerReadModel
    {
        return TrackerReadModel::hydrateFromModel($this->trackerRepository->getById($id));
    }

    public function FullById(int $id): TrackerReadModel
    {
        $tracker = $this->trackerRepository->getById($id);
        $trackerReadModelFactory = new TrackerReadModelFactory($tracker);
        return $trackerReadModelFactory->getCompleteReadModel();
    }

    // -- get list of issues of the related teams ----------------------------------------

    /**
     * @throws \Exception
     */
    public function IssuesById(int $id): IssueCacheReadModelCollection
    {
        $tracker = $this->trackerRepository->getById($id);
        $collection = new IssueCacheReadModelCollection();

        if (strlen($tracker->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
            $this->linearConfig->setKey($tracker->getProject()->getOrganisation()->getLinearApiKey());
            $getIssues = new GetIssues($this->linearConfig);
            $collection = $getIssues->ByTeams($tracker->getRelatedTeams());
        }

        return $collection;
    }

    // -- render the tracker as pdf -------------------------------------------------------

    public function renderTrackerPdf(int $id): TrackerReadModel
    {
        $tracker = $this->trackerRepository->getById($id);
        $trackerFactory = new TrackerReadModelFactory($tracker);
        $trackerFlattened = $trackerFactory->getFlattenedReadModel();

        // ------------------------------------------------------------ convert the flattened tracker to a render object
        $renderTracker = new \stdClass();
        $renderTracker->name = $tracker->getName();
        $renderTracker->code = $tracker->getProject()->getCode() . '-' . $tracker->getCode();
        $renderTracker->uuid = $tracker->getUuid()->toRfc4122();
        $renderTracker->description = $tracker->getDescription();
        $renderTracker->projectDescription = $tracker->getProject()->getProject()->getDescription();
        $renderTracker->generatedOn = new \DateTimeImmutable();
        $renderTracker->version = 'HEAD';
        $renderTracker->elements = [];

        // -- project & logo
        $renderTracker->project = $tracker->getProject()->getName();
        if ($tracker->getProject()->getProject()->getLogo() !== '' && file_exists($this->uploadFolder . $tracker->getProject()->getProject()->getLogo())) {
            $renderTracker->logo = base64_encode(file_get_contents($this->uploadFolder . $tracker->getProject()->getProject()->getLogo()));
        } else {
            $renderTracker->logo = '';
        }

        foreach ($trackerFlattened->getElements() as $element) {
            $renderElement = new \stdClass();
            if ($element instanceof TrackerNodeReadModel) {
                $renderElement->number = $element->getNumber();
                $renderElement->code = '[' . $tracker->getCode() . '-' . $element->getArtefactId() . ']';
                $renderElement->status = $element->getTrackerNodeStatusReadModel()->getName();
                $renderElement->uuid = $element->getUuid();
                $renderElement->statusColor = '#' . $element->getTrackerNodeStatusReadModel()->getColor();
                $renderElement->rawTitle = $element->getName();
                $renderElement->title = $this->renderTitle($element->getName(), $element->getNumber());
                $renderElement->tocTitle = str_pad($element->getNumber(), 25 + ($element->getLevel() * 3), '.', STR_PAD_RIGHT) . ' ' . $element->getName();
                $renderElement->content = $this->filterContent($element->getDescription());
                $renderElement->type = 'element';
                $renderElement->files = [];

                if (count($element->getFileReadModelCollection()->getCollection()) !== 0) {
                    /** @var TrackerNodeFileReadModel $file */
                    foreach ($element->getFileReadModelCollection()->getCollection() as $trackerFile) {
                        $file = new \stdClass();
                        $file->artefactId = $trackerFile->getArtefactId();
                        $file->name = $trackerFile->getName();
                        $file->data = base64_encode(file_get_contents($this->uploadFolder.$trackerFile->getSrcPath()));
                        $renderElement->files[] = $file;
                    }
                }

            } elseif ($element instanceof TrackerNodeGroupReadModel) {
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
            $renderTracker->elements[] = $renderElement;
        }

        $pdfHtml = $this->twig->render('@lodocio/Pdf/tracker-pdf-default.html.twig', ['tracker' => $renderTracker]);

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
        $fileName = strtolower((string)$slugger->slug(trim(CamelConverter::CamelToKebab($tracker->getName()))));
        $fileName = $renderTracker->generatedOn->format('Ymd') . '_' . $renderTracker->code . '_' . $fileName . '_' . $renderTracker->version . '.pdf';

        $dompdf->stream($fileName, array("Attachment" => false));
        exit();

        return TrackerReadModel::hydrateFromModel($tracker);
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

}
