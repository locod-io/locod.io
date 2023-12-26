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

namespace App\Lodocio\Application\Command\Wiki\SyncWikiStructure;

use App\Lodocio\Domain\Model\Project\DocProjectRepository;
use App\Lodocio\Domain\Model\Wiki\DTO\WikiStructureGroup;
use App\Lodocio\Domain\Model\Wiki\DTO\WikiStructureNode;
use App\Lodocio\Domain\Model\Wiki\Wiki;
use App\Lodocio\Domain\Model\Wiki\WikiNode;
use App\Lodocio\Domain\Model\Wiki\WikiNodeGroup;
use App\Lodocio\Domain\Model\Wiki\WikiNodeGroupRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatusRepository;
use App\Lodocio\Domain\Model\Wiki\WikiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class SyncWikiStructureHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected EntityManagerInterface   $entityManager,
        protected DocProjectRepository     $docProjectRepository,
        protected WikiRepository           $wikiRepository,
        protected WikiNodeStatusRepository $wikiNodeStatusRepository,
        protected WikiNodeRepository       $wikiNodeRepository,
        protected WikiNodeGroupRepository  $wikiNodeGroupRepository,
    ) {
    }

    public function go(SyncWikiStructure $command): bool
    {
        $wiki = $this->wikiRepository->getById($command->getId());
        $wiki->setRawStructure($command->getStructure());
        $this->wikiRepository->save($wiki);

        // -- sync the root nodes
        $this->syncNodes($command->getStructure()->nodes, $wiki);

        // -- sync the root groups
        $this->syncGroups($command->getStructure()->groups, $wiki);

        return true;
    }

    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————
    // Private synchronisation functions
    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————

    private function syncNodes(array $nodes, Wiki $wiki): void
    {
        foreach ($nodes as $node) {
            $syncNode = WikiStructureNode::hydrateFromJson($node);
            $this->syncNode($syncNode, $wiki);
        }
    }

    private function syncGroups(array $groups, Wiki $wiki): void
    {
        foreach ($groups as $group) {
            $this->syncGroup($group, $wiki);
        }
    }

    private function syncNode(WikiStructureNode $command, Wiki $wiki): void
    {
        $node = $this->wikiNodeRepository->findByUuid(Uuid::fromString($command->getUuid()));
        $firstStatus = $this->wikiNodeStatusRepository->findFirstStatus($wiki);

        // todo LODO-5 check if there is a first status

        if (is_null($node)) {
            $newNode = WikiNode::make(
                $wiki,
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
            $newNode->setArtefactId($this->wikiNodeRepository->getNextArtefactId($wiki));
            $this->wikiNodeRepository->save($newNode);
        } else {
            $node->sync(
                $command->getLevel(),
                $command->getNumber(),
                $command->isOpen()
            );
            $this->wikiNodeRepository->save($node);
        }
    }

    private function syncGroup($groupJson, Wiki $wiki): void
    {
        $command = WikiStructureGroup::hydrateFromJson($groupJson);
        $group = $this->wikiNodeGroupRepository->findByUuid(Uuid::fromString($command->getUuid()));
        if (is_null($group)) {
            $newGroup = WikiNodeGroup::make(
                $wiki,
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
            $newGroup->setArtefactId($this->wikiNodeGroupRepository->getNextArtefactId($wiki));
            $this->wikiNodeGroupRepository->save($newGroup);
        } else {
            $group->sync(
                $command->getLevel(),
                $command->getNumber(),
                $command->isOpen(),
            );
            $this->wikiNodeGroupRepository->save($group);
        }

        // -- recursive sync elements and groups
        $this->syncNodes($groupJson->nodes, $wiki);
        $this->syncGroups($groupJson->groups, $wiki);
    }

}
