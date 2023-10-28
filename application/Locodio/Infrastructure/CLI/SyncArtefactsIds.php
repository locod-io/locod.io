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

namespace App\Locodio\Infrastructure\CLI;

use App\Locodio\Domain\Model\Model\Association;
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\Command as LocodioCommand;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Model\ModuleRepository;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\Model\QueryRepository;
use App\Locodio\Domain\Model\Model\AssociationRepository;
use App\Locodio\Domain\Model\Model\AttributeRepository;
use App\Locodio\Domain\Model\Model\CommandRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\EnumOptionRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncArtefactsIds extends Command
{
    private readonly EntityManagerInterface $entityManager;
    private readonly DomainModelRepository $domainModelRepository;
    private readonly AttributeRepository $attributeRepository;
    private readonly AssociationRepository $associationRepository;
    private readonly EnumRepository $enumRepository;
    private readonly EnumOptionRepository $enumOptionRepository;
    private readonly CommandRepository $commandRepository;
    private readonly QueryRepository $queryRepository;
    private readonly ModuleRepository $moduleRepository;

    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct('locodio:sync:artefact');
        $this->entityManager = $entityManager;
        $this->domainModelRepository = $entityManager->getRepository(DomainModel::class);
        $this->attributeRepository = $entityManager->getRepository(Attribute::class);
        $this->associationRepository = $entityManager->getRepository(Association::class);
        $this->enumRepository = $entityManager->getRepository(Enum::class);
        $this->enumOptionRepository = $entityManager->getRepository(EnumOption::class);
        $this->commandRepository = $entityManager->getRepository(LocodioCommand::class);
        $this->queryRepository = $entityManager->getRepository(Query::class);
        $this->moduleRepository = $entityManager->getRepository(Module::class);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Executor
    // ——————————————————————————————————————————————————————————————————————————

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->domainModelRepository->findAll() as $model) {
            if($model->getArtefactId() === 0) {
                $model->setArtefactId($this->domainModelRepository->getNextArtefactId($model->getProject()));
                $this->entityManager->flush();
                $output->writeln('DM "' . $model->getName() . '" is synced as artefact.');
            }
        }

        foreach ($this->attributeRepository->findAll() as $model) {
            if($model->getArtefactId() === 0) {
                $model->setArtefactId($this->attributeRepository->getNextArtefactId($model->getDomainModel()->getProject()));
                $this->entityManager->flush();
                $output->writeln('AT "' . $model->getName() . '" is synced as artefact.');
            }
        }

        foreach ($this->associationRepository->findAll() as $model) {
            if($model->getArtefactId() === 0) {
                $model->setArtefactId($this->associationRepository->getNextArtefactId($model->getDomainModel()->getProject()));
                $this->entityManager->flush();
                $output->writeln('AS "' . $model->getId() . '" is synced as artefact.');
            }
        }

        foreach ($this->enumRepository->findAll() as $model) {
            if($model->getArtefactId() === 0) {
                $model->setArtefactId($this->enumOptionRepository->getNextArtefactId($model->getProject()));
                $this->entityManager->flush();
                $output->writeln('E "' . $model->getName() . '" is synced as artefact.');
            }
        }

        foreach ($this->enumOptionRepository->findAll() as $model) {
            if($model->getArtefactId() === 0) {
                $model->setArtefactId($this->enumOptionRepository->getNextArtefactId($model->getEnum()->getProject()));
                $this->entityManager->flush();
                $output->writeln('EO "' . $model->getCode() . '" is synced as artefact.');
            }
        }

        foreach ($this->queryRepository->findAll() as $model) {
            if($model->getArtefactId() === 0) {
                $model->setArtefactId($this->queryRepository->getNextArtefactId($model->getProject()));
                $this->entityManager->flush();
                $output->writeln('Q "' . $model->getName() . '" is synced as artefact.');
            }
        }

        foreach ($this->commandRepository->findAll() as $model) {
            if($model->getArtefactId() === 0) {
                $model->setArtefactId($this->commandRepository->getNextArtefactId($model->getProject()));
                $this->entityManager->flush();
                $output->writeln('C "' . $model->getName() . '" is synced as artefact.');
            }
        }

        foreach ($this->moduleRepository->findAll() as $model) {
            if($model->getArtefactId() === 0) {
                $model->setArtefactId($this->moduleRepository->getNextArtefactId($model->getProject()));
                $this->entityManager->flush();
                $output->writeln('M "' . $model->getName() . '" is synced as artefact.');
            }
        }

        return 0;
    }
}
