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

namespace App\Lodocio\Application\Command\Tracker\AddTracker;

use App\Lodocio\Domain\Model\Project\DocProjectRepository;
use App\Lodocio\Domain\Model\Tracker\DTO\TrackerStructure;
use App\Lodocio\Domain\Model\Tracker\DTO\TrackerStructureGroup;
use App\Lodocio\Domain\Model\Tracker\DTO\TrackerStructureNode;
use App\Lodocio\Domain\Model\Tracker\Tracker;
use App\Lodocio\Domain\Model\Tracker\TrackerNode;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeGroup;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeGroupRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatus;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatusRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddTrackerHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected EntityManagerInterface      $entityManager,
        protected DocProjectRepository        $docProjectRepository,
        protected TrackerRepository           $trackerRepository,
        protected TrackerNodeStatusRepository $trackerNodeStatusRepository,
        protected TrackerNodeRepository       $trackerNodeRepository,
        protected TrackerNodeGroupRepository  $trackerNodeGroupRepository,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————————————

    public function go(AddTracker $command): bool
    {
        $project = $this->docProjectRepository->getById($command->getDocProjectId());

        $model = Tracker::make(
            $project,
            $this->trackerRepository->nextIdentity(),
            $command->getName(),
            $command->getCode(),
            '#' . $command->getColor(),
        );
        $model->setSequence($this->trackerRepository->getMaxSequence($project));
        $model->setArtefactId($this->trackerRepository->getNextArtefactId($project));
        $trackerStructure = new TrackerStructure();
        $model->setStructure($trackerStructure);
        $id = $this->trackerRepository->save($model);
        $this->entityManager->flush();

        // -- make a start status

        $startStatus = TrackerNodeStatus::make(
            $model,
            $this->trackerNodeStatusRepository->nextIdentity(),
            'New',
            'c0c0c0',
            true,
            false,
        );
        $startStatus->setSequence(0);
        $startStatus->setArtefactId(1);
        $this->trackerNodeStatusRepository->save($startStatus);
        $this->entityManager->flush();

        // -- make an end status

        $endStatus = TrackerNodeStatus::make(
            $model,
            $this->trackerNodeStatusRepository->nextIdentity(),
            'Final',
            '008000',
            false,
            true,
        );
        $endStatus->setSequence(1);
        $endStatus->setArtefactId(2);
        $this->trackerNodeStatusRepository->save($endStatus);
        $this->entityManager->flush();

        // -- make a first node

        $firstNodeUuid = $this->trackerNodeRepository->nextIdentity();
        $firstNode = TrackerNode::make(
            $model,
            $firstNodeUuid,
            $startStatus,
            'my first item',
            0,
            '1'
        );
        $firstNode->setSequence(0);
        $firstNode->setArtefactId(1);
        $this->trackerNodeRepository->save($firstNode);

        // -- make a first group

        $firstGroupUuid = $this->trackerNodeGroupRepository->nextIdentity();
        $firstGroup = TrackerNodeGroup::make(
            $model,
            $firstGroupUuid,
            'my first group',
            0,
            '2'
        );
        $firstGroup->setSequence(0);
        $firstGroup->setArtefactId(1);
        $this->trackerNodeGroupRepository->save($firstGroup);

        // create a structure object and register it

        $trackerStructure->addNode(TrackerStructureNode::hydrateFromModel($firstNode));
        $trackerStructure->addGroup(TrackerStructureGroup::hydrateFromModel($firstGroup));
        $model->setStructure($trackerStructure);

        $id = $this->trackerRepository->save($model);

        return true;
    }

}
