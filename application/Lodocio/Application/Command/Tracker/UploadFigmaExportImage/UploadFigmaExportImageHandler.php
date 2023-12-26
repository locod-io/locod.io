<?php

declare(strict_types=1);

namespace App\Lodocio\Application\Command\Tracker\UploadFigmaExportImage;

use App\Figma\Application\Query\Files\GetImages;
use App\Figma\FigmaConfig;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeFile;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeFileRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadFigmaExportImageHandler
{
    public function __construct(
        protected TrackerNodeRepository     $nodeRepository,
        protected TrackerNodeFileRepository $fileRepository,
        protected FigmaConfig               $figmaConfig,
        protected string                    $uploadsFolder,
    ) {
    }

    /**
     * @throws \Exception
     * @throws GuzzleException
     */
    public function uploadFigmaFiles(UploadFigmaExportImage $command): bool
    {
        $node = $this->nodeRepository->getById($command->getNodeId());
        if (strlen($node->getTracker()->getProject()->getProject()->getOrganisation()->getFigmaApiKey()) !== 0) {
            $this->figmaConfig->setKey($node->getTracker()->getProject()->getProject()->getOrganisation()->getFigmaApiKey());
        }
        FigmaConfig::checkConfig($this->figmaConfig);

        $getImages = new GetImages($this->figmaConfig);
        $figmaImages = $getImages->forDocument($command->getFigmaDocumentKey());

        $uploadFolder = 'O-' . $node->getTracker()->getProject()->getProject()->getOrganisation()->getId() .
            '/P-' . $node->getTracker()->getProject()->getId() .
            '/W-' . $node->getTracker()->getId() . '/';

        // -- make the folder wiki folder...
        if (!file_exists($this->uploadsFolder . $uploadFolder)) {
            mkdir($this->uploadsFolder . $uploadFolder, 0777, true);
        }

        $artefactId = $this->fileRepository->getNextArtefactId($node->getTracker());
        foreach ($figmaImages as $key => $value) {
            $uploadFile = $command->getFigmaDocumentKey() . '_' . $key . '.png';
            try {
                if (file_exists($this->uploadsFolder . $uploadFolder . $uploadFile)) {
                    unlink($this->uploadsFolder . $uploadFolder . $uploadFile);
                }
                file_put_contents($this->uploadsFolder . $uploadFolder . $uploadFile, file_get_contents($value));
                $trackerFile = $this->fileRepository->findByTrackerNodeAndName($node, $command->getFigmaDocumentKey() . $key);
                if (true === is_null($trackerFile)) {
                    $wikiFile = TrackerNodeFile::make(
                        $node,
                        $this->fileRepository->nextIdentity(),
                        $artefactId,
                        0,
                        $command->getFigmaDocumentKey() . $key,
                        $uploadFile,
                        $uploadFolder . $uploadFile,
                        '',
                    );
                    $this->fileRepository->save($wikiFile);
                }
            } catch (FileException $e) {
                throw new \Exception('Could not download the image');
                return false;
            }
            $artefactId = $artefactId + 1;
        }
        return true;
    }
}
