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

namespace App\Lodocio\Infrastructure\CLI;

use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Lodocio\Domain\Model\Project\DocProject;
use App\Lodocio\Domain\Model\Project\DocProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncDocProjectCommand extends Command
{
    private readonly EntityManagerInterface $entityManager;
    private readonly ProjectRepository $projectRepository;
    private readonly DocProjectRepository $docProjectRepository;

    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct('lodocio:sync:projects');

        $this->entityManager = $entityManager;

        /** @var ProjectRepository $projectRepository */
        $this->projectRepository =  $entityManager->getRepository(Project::class);
        /** @var DocProjectRepository $docProjectRepository */
        $this->docProjectRepository = $entityManager->getRepository(DocProject::class);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Executor
    // ——————————————————————————————————————————————————————————————————————————

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projects = $this->projectRepository->getAll();
        /** @var Project $project */
        foreach ($projects as $project) {
            $docProject = $this->docProjectRepository->findByProject($project);
            if (is_null($docProject)) {
                $docProject = DocProject::make(
                    $this->docProjectRepository->nextIdentity(),
                    $project->getName(),
                    $project->getCode(),
                    $project->getOrganisation(),
                    $project
                );
                $this->docProjectRepository->save($docProject);
                $output->writeln('Project "' . $project->getName() . '" is synced as doc-project.');
                $this->entityManager->flush();
            }
        }
        return 0;
    }
}
