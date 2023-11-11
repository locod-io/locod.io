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

namespace App\Lodocio\Application\Command\Tracker\SyncTrackerStructure;

use App\Lodocio\Domain\Model\Project\DocProjectRepository;
use App\Lodocio\Domain\Model\Tracker\DTO\TrackerStructureGroup;
use App\Lodocio\Domain\Model\Tracker\DTO\TrackerStructureNode;
use App\Lodocio\Domain\Model\Tracker\Tracker;
use App\Lodocio\Domain\Model\Tracker\TrackerNode;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeGroup;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeGroupRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatusRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class SyncTrackerStructureHandler
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

    public function go(SyncTrackerStructure $command): bool
    {
        $tracker = $this->trackerRepository->getById($command->getId());
        $tracker->setRawStructure($command->getStructure());
        $this->trackerRepository->save($tracker);

        // -- sync the root nodes
        $this->syncNodes($command->getStructure()->nodes, $tracker);

        // -- sync the root groups
        $this->syncGroups($command->getStructure()->groups, $tracker);

        return true;
    }

    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————
    // Private synchronisation functions
    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————

    private function syncNodes(array $nodes, Tracker $tracker): void
    {
        foreach ($nodes as $node) {
            $syncNode = TrackerStructureNode::hydrateFromJson($node);
            $this->syncNode($syncNode, $tracker);
        }
    }

    private function syncGroups(array $groups, Tracker $tracker): void
    {
        foreach ($groups as $group) {
            $this->syncGroup($group, $tracker);
        }
    }

    private function syncNode(TrackerStructureNode $command, Tracker $tracker): void
    {
        $node = $this->trackerNodeRepository->findByUuid(Uuid::fromString($command->getUuid()));
        $firstStatus = $this->trackerNodeStatusRepository->findFirstStatus($tracker);

        // todo LODO-5 check if there is a first status

        if (is_null($node)) {
            $newNode = TrackerNode::make(
                $tracker,
                Uuid::fromString($command->getUuid()),
                $firstStatus,
                $command->getName(),
                $command->getLevel(),
                $command->getNumber(),
            );
            $newNode->sync(
                $command->getLevel(),
                $command->getNumber(),
                $command->isOpen()
            );
            $newNode->setArtefactId($this->trackerNodeRepository->getNextArtefactId($tracker));
            $this->trackerNodeRepository->save($newNode);
        } else {
            $node->sync(
                $command->getLevel(),
                $command->getNumber(),
                $command->isOpen()
            );
            $this->trackerNodeRepository->save($node);
        }
    }

    private function syncGroup($groupJson, Tracker $tracker): void
    {
        $command = TrackerStructureGroup::hydrateFromJson($groupJson);
        $group = $this->trackerNodeGroupRepository->findByUuid(Uuid::fromString($command->getUuid()));
        if (is_null($group)) {
            $newGroup = TrackerNodeGroup::make(
                $tracker,
                Uuid::fromString($command->getUuid()),
                $command->getName(),
                $command->getLevel(),
                $command->getNumber(),
            );
            $newGroup->sync(
                $command->getLevel(),
                $command->getNumber(),
                $command->isOpen(),
            );
            $newGroup->setArtefactId($this->trackerNodeGroupRepository->getNextArtefactId($tracker));
            $this->trackerNodeGroupRepository->save($newGroup);
        } else {
            $group->sync(
                $command->getLevel(),
                $command->getNumber(),
                $command->isOpen(),
            );
            $this->trackerNodeGroupRepository->save($group);
        }

        // -- recursive sync elements and groups
        $this->syncNodes($groupJson->nodes, $tracker);
        $this->syncGroups($groupJson->groups, $tracker);
    }

}
