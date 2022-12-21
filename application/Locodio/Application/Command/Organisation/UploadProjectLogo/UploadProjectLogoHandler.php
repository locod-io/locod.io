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

namespace App\Locodio\Application\Command\Organisation\UploadProjectLogo;

use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadProjectLogoHandler
{
    public function __construct(
        protected UserRepository $userRepository,
        protected ProjectRepository $projectRepo
    ) {
    }

    public function go(UploadProjectLogo $command): bool
    {
        $user = $this->userRepository->getById($command->getUserId());
        $project = $this->projectRepo->getById($command->getProjectId());
        $logo = $command->getFile();
        $extension = strtolower($logo->guessExtension());

        // -- check the extension and filesize
        if (!($extension === 'png'
            || $extension === 'jpeg'
            || $extension === 'jpg'
            || $extension === 'gif')) {
            throw new \Exception('Could not save the uploaded file');
            return false;
        }

        $filesize = filesize($logo->getRealPath());
        if ($filesize > 2000000) {
            throw new \Exception('Could not save the uploaded file');
            return false;
        }

        $uploadFolder = 'U-'.$command->getUserId().'/P-'.$command->getProjectId().'/';
        // -- make the folder
        if (!file_exists($command->getUploadsFolder().$uploadFolder)) {
            mkdir($command->getUploadsFolder().$uploadFolder, 0777, true);
        }
        $uploadFile = '_logo.'.$extension;
        try {
            $logo->move($command->getUploadsFolder().$uploadFolder, $uploadFile);
        } catch (FileException $e) {
            throw new \Exception('Could not copy the uploaded file');
            return false;
        }
        $project->setLogo($uploadFolder.$uploadFile);
        $this->projectRepo->save($project);

        return true;
    }
}
