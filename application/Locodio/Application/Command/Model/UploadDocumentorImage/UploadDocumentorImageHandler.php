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

namespace App\Locodio\Application\Command\Model\UploadDocumentorImage;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\CommandRepository;
use App\Locodio\Domain\Model\Model\DocumentorType;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\ModuleRepository;
use App\Locodio\Domain\Model\Model\QueryRepository;
use App\Locodio\Infrastructure\Database\DocumentorRepository;
use App\Lodocio\Application\Helper\SimpleImage;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadDocumentorImageHandler
{
    public function __construct(
        protected DocumentorRepository  $documentorRepo,
        protected ModuleRepository      $moduleRepo,
        protected DomainModelRepository $domainModelRepo,
        protected EnumRepository        $enumRepo,
        protected QueryRepository       $queryRepo,
        protected CommandRepository     $commandRepo
    ) {
    }

    /**
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    public function go(UploadDocumentorImage $command): bool
    {
        $documentor = $this->documentorRepo->getById($command->getDocumentorId());

        if (ModelFinalChecker::isFinalState($documentor)) {
            return false;
        }

        switch ($documentor->getType()) {
            case DocumentorType::DOMAIN_MODEL:
                $subject = $this->domainModelRepo->getByDocumentor($documentor);
                $subjectCode = 'DM-' . $subject->getId();
                $project = $subject->getProject();
                break;
            case DocumentorType::ENUM:
                $subject = $this->enumRepo->getByDocumentor($documentor);
                $subjectCode = 'E-' . $subject->getId();
                $project = $subject->getProject();
                break;
            case DocumentorType::QUERY:
                $subject = $this->queryRepo->getByDocumentor($documentor);
                $subjectCode = 'Q-' . $subject->getId();
                $project = $subject->getProject();
                break;
            case DocumentorType::COMMAND:
                $subject = $this->commandRepo->getByDocumentor($documentor);
                $subjectCode = 'C-' . $subject->getId();
                $project = $subject->getProject();
                break;
            default:
                $subject = $this->moduleRepo->getByDocumentor($documentor);
                $subjectCode = 'M-' . $subject->getId();
                $project = $subject->getProject();
                break;
        }

        $image = $command->getFile();
        $extension = strtolower($image->guessExtension());

        // -- check the extension and filesize
        if (!($extension === 'png'
            || $extension === 'jpeg'
            || $extension === 'jpg'
            || $extension === 'gif')) {
            throw new \Exception('Could not save the uploaded file');
            return false;
        }
        $filesize = filesize($image->getRealPath());
        if ($filesize > 3000000) {
            throw new \Exception('Could not save the uploaded file');
            return false;
        }

        // -- test if the upload is a real image
        $image = SimpleImage::load($file->getRealPath());

        // -- make the upload folder
        $uploadFolder = 'P-' . $project->getId() . '/' . $subjectCode . '/';
        if (!file_exists($command->getUploadsFolder() . $uploadFolder)) {
            mkdir($command->getUploadsFolder() . $uploadFolder, 0777, true);
        }
        $documentorId = strval($documentor->getId());
        $uploadFile = $subjectCode . '_doc-' . $documentorId . '.' . $extension;
        try {
            $image->move($command->getUploadsFolder() . $uploadFolder, $uploadFile);
        } catch (FileException $e) {
            throw new \Exception('Could not copy the uploaded file');
            return false;
        }

        $documentor->setImage($uploadFolder . $uploadFile);
        $this->documentorRepo->save($documentor);

        return true;
    }
}
