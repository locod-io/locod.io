<?php

/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Locodio\Application\Query\Model;

use App\Locodio\Application\Query\Model\Readmodel\CommandRM;
use App\Locodio\Application\Query\Model\Readmodel\DomainModelRM;
use App\Locodio\Application\Query\Model\Readmodel\EnumRM;
use App\Locodio\Application\Query\Model\Readmodel\ModuleRM;
use App\Locodio\Application\Query\Model\Readmodel\ProjectDocumentation;
use App\Locodio\Application\Query\Model\Readmodel\ProjectDocumentationItem;
use App\Locodio\Application\Query\Model\Readmodel\QueryRM;
use App\Locodio\Domain\Model\Model\CommandRepository;
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Model\QueryRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Twig\Environment;

class GetDocumentation
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository     $projectRepo,
        protected DomainModelRepository $domainModelRepo,
        protected EnumRepository        $enumRepo,
        protected QueryRepository       $queryRepo,
        protected CommandRepository     $commandRepo,
        protected Environment           $twig,
        protected string                $uploadFolder,
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Queries
    // ——————————————————————————————————————————————————————————————————————————

    public function ByProjectId(int $id, bool $forPdf = false): ProjectDocumentation
    {
        $documentation = new ProjectDocumentation([]);
        $project = $this->projectRepo->getById($id);

        $index = 1;
        foreach ($project->getModules() as $module) {
            $docItem = new ProjectDocumentationItem(
                $module->getId(),
                $module->getName(),
                1,
                $index . '.',
                'module',
                'M',
                'M-'.$module->getArtefactId(),
                ModuleRM::hydrateFromModel($module, true)
            );

            // -- read some image information for pdf rendering
            if ($forPdf) {
                $imageInformation = $this->getDocumentorImage($module->getDocumentor());
                if ($imageInformation->image !== '') {
                    $docItem->setDocumentorImageData($imageInformation->image, $imageInformation->orientation);
                }
            }

            $documentation->addItem($docItem);
            $documentation->addItems($this->getDomainModelByModule($module, $index . '.', $forPdf));
            $index++;
        }

        return $documentation;
    }

    public function DownloadDocumentionByProjectId(int $id): void
    {
        $project = $this->projectRepo->getById($id);
        $documentation = $this->ByProjectId($id, true);

        $renderProject = new \stdClass();
        $renderProject->name = $project->getName();
        $renderProject->code = $project->getCode();
        $renderProject->uuid = $project->getUuid();
        $renderProject->generatedOn = new \DateTimeImmutable();
        $renderProject->version = 'HEAD';

        // -- project logo
        if ($project->getLogo() !== '' && file_exists($this->uploadFolder . $project->getLogo())) {
            $renderProject->logo = base64_encode(file_get_contents($this->uploadFolder . $project->getLogo()));
        } else {
            $renderProject->logo = '';
        }

        // -- flattened documentation list
        $renderProject->documentation = $documentation->getItems();

        // -- render documentation in html
        $pdfHtml = $this->twig->render('Pdf/model-documentation.html.twig', ['project' => $renderProject]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($pdfHtml);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $slugger = new AsciiSlugger();
        $fileName = strtolower($slugger->slug(trim($project->getName()))->toString());
        $fileName = $renderProject->generatedOn->format('Ymd') . '_DataModel_' . $fileName . '_' . $renderProject->version . '.pdf';

        $dompdf->stream($fileName, array("Attachment" => false));

        exit();
    }

    // -------------------------------------------------------------------------------------------------
    // private functions
    // -------------------------------------------------------------------------------------------------

    private function getDocumentorImage(?Documentor $documentor): \stdClass
    {
        $result = new \stdClass();
        if (!is_null($documentor) && !is_null($documentor->getImage()) && $documentor->getImage() !== '') {
            $result->image = base64_encode(file_get_contents($this->uploadFolder . $documentor->getImage()));
            $result->orientation = 'landscape';
            $imageSize = getimagesize($this->uploadFolder . $documentor->getImage());
            $imageRatio = $imageSize[0] / $imageSize[1];
            if ($imageRatio < 1.5) {
                $result->orientation = 'portrait';
            }
        } else {
            $result->image = '';
        }
        return $result;
    }

    private function getDomainModelByModule(Module $module, string $parentLabel, bool $forPdf): array
    {
        $result = [];
        $index = 1;
        $domainModels = $this->domainModelRepo->getByModule($module);
        foreach ($domainModels as $domainModel) {
            $docItem = new ProjectDocumentationItem(
                $domainModel->getId(),
                $domainModel->getName(),
                2,
                $parentLabel . $index . '.',
                'domain-model',
                'DM',
                'DM-'.$domainModel->getArtefactId(),
                DomainModelRM::hydrateFromModel($domainModel, true)
            );

            // -- read some image information for pdf rendering
            if ($forPdf) {
                $imageInformation = $this->getDocumentorImage($domainModel->getDocumentor());
                if ($imageInformation->image !== '') {
                    $docItem->setDocumentorImageData($imageInformation->image, $imageInformation->orientation);
                }
            }
            $result[] = $docItem;

            foreach ($this->getDomainModelRelations($domainModel, $parentLabel . $index . '.', $forPdf) as $item) {
                $result[] = $item;
            }
            $index++;
        }
        return $result;
    }

    private function getDomainModelRelations(DomainModel $domainModel, string $parentLabel, bool $forPdf): array
    {
        $result = [];
        $index = 1;
        if ($forPdf) {
            $attributesDocItem = new ProjectDocumentationItem(
                $domainModel->getId(),
                'Attributes',
                3,
                $parentLabel . $index . '.',
                'attributes',
                'AT',
                'DM-'.$domainModel->getArtefactId(),
                DomainModelRM::hydrateFromModel($domainModel, true)
            );
        } else {
            $attributesDocItem = new ProjectDocumentationItem(
                $domainModel->getId(),
                'Attributes',
                3,
                $parentLabel . $index . '.',
                'attributes',
                'AT',
                'DM-'.$domainModel->getArtefactId(),
            );
        }
        $result[] = $attributesDocItem;
        $index++;
        if ($forPdf) {
            $associationsDocItem = new ProjectDocumentationItem(
                $domainModel->getId(),
                'Associations',
                3,
                $parentLabel . $index . '.',
                'associations',
                'AS',
                'DM-'.$domainModel->getArtefactId(),
                DomainModelRM::hydrateFromModel($domainModel, true)
            );
        } else {
            $associationsDocItem = new ProjectDocumentationItem(
                $domainModel->getId(),
                'Associations',
                3,
                $parentLabel . $index . '.',
                'associations',
                'AS',
                'DM-'.$domainModel->getArtefactId(),
            );
        }
        $result[] = $associationsDocItem;

        // -- if enums render enums
        $index++;
        $enums = $this->getEnumDocumentation($domainModel, $parentLabel . $index . '.', $forPdf);
        if (count($enums) > 0) {
            $enumDocItem = new ProjectDocumentationItem(
                0,
                'Enums',
                3,
                $parentLabel . $index . '.',
                'item',
                '',
                '',
            );
            $result[] = $enumDocItem;
            foreach ($enums as $enum) {
                $result[] = $enum;
            }
        } else {
            $index--;
        }

        // if queries render queries
        $index++;
        $queries = $this->getQueryDocumentation($domainModel, $parentLabel . $index . '.', $forPdf);
        if (count($queries) > 0) {
            $queryDocItem = new ProjectDocumentationItem(
                0,
                'Queries',
                3,
                $parentLabel . $index . '.',
                'item',
                '',
                ''
            );
            $result[] = $queryDocItem;
            foreach ($queries as $query) {
                $result[] = $query;
            }
        } else {
            $index--;
        }

        // if commands render commands
        $index++;
        $commands = $this->getCommandDocumentation($domainModel, $parentLabel . $index . '.', $forPdf);
        if (count($commands) > 0) {
            $commandDocItem = new ProjectDocumentationItem(
                0,
                'Commands',
                3,
                $parentLabel . $index . '.',
                'item',
                '',
                ''
            );
            $result[] = $commandDocItem;
            foreach ($commands as $command) {
                $result[] = $command;
            }
        } else {
            $index--;
        }

        return $result;
    }

    private function getEnumDocumentation(DomainModel $domainModel, string $parentLabel, bool $forPdf): array
    {
        $result = [];
        $index = 1;
        $enums = $this->enumRepo->getByDomainModel($domainModel);
        foreach ($enums as $enum) {
            $docItem = new ProjectDocumentationItem(
                $enum->getId(),
                $enum->getName(),
                4,
                $parentLabel . $index . '.',
                'enum',
                'E',
                'E-'.$enum->getArtefactId(),
                EnumRM::hydrateFromModel($enum, true),
            );

            // -- read some image information for pdf rendering
            if ($forPdf) {
                $imageInformation = $this->getDocumentorImage($enum->getDocumentor());
                if ($imageInformation->image !== '') {
                    $docItem->setDocumentorImageData($imageInformation->image, $imageInformation->orientation);
                }
            }

            $result[] = $docItem;
            $index++;
        }
        return $result;
    }

    private function getQueryDocumentation(DomainModel $domainModel, string $parentLabel, bool $forPdf): array
    {
        $result = [];
        $index = 1;
        $queries = $this->queryRepo->getByDomainModel($domainModel);
        foreach ($queries as $query) {
            $docItem = new ProjectDocumentationItem(
                $query->getId(),
                $query->getName(),
                4,
                $parentLabel . $index . '.',
                'query',
                'Q',
                'Q-'.$query->getArtefactId(),
                QueryRM::hydrateFromModel($query, true)
            );

            // -- read some image information for pdf rendering
            if ($forPdf) {
                $imageInformation = $this->getDocumentorImage($query->getDocumentor());
                if ($imageInformation->image !== '') {
                    $docItem->setDocumentorImageData($imageInformation->image, $imageInformation->orientation);
                }
            }

            $result[] = $docItem;
            $index++;
        }

        return $result;
    }

    private function getCommandDocumentation(DomainModel $domainModel, string $parentLabel, bool $forPdf): array
    {
        $result = [];
        $index = 1;
        $commands = $this->commandRepo->getByDomainModel($domainModel);
        foreach ($commands as $command) {
            $docItem = new ProjectDocumentationItem(
                $command->getId(),
                $command->getName(),
                4,
                $parentLabel . $index . '.',
                'command',
                'C',
                'C-'.$command->getArtefactId(),
                CommandRM::hydrateFromModel($command, true)
            );

            // -- read some image information for pdf rendering
            if ($forPdf) {
                $imageInformation = $this->getDocumentorImage($command->getDocumentor());
                if ($imageInformation->image !== '') {
                    $docItem->setDocumentorImageData($imageInformation->image, $imageInformation->orientation);
                }
            }

            $result[] = $docItem;
            $index++;
        }

        return $result;
    }
}
