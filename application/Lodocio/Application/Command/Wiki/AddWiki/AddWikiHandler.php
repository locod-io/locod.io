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

namespace App\Lodocio\Application\Command\Wiki\AddWiki;

use App\Lodocio\Domain\Model\Project\DocProjectRepository;
use App\Lodocio\Domain\Model\Wiki\DTO\WikiStructure;
use App\Lodocio\Domain\Model\Wiki\DTO\WikiStructureGroup;
use App\Lodocio\Domain\Model\Wiki\DTO\WikiStructureNode;
use App\Lodocio\Domain\Model\Wiki\Wiki;
use App\Lodocio\Domain\Model\Wiki\WikiNode;
use App\Lodocio\Domain\Model\Wiki\WikiNodeGroup;
use App\Lodocio\Domain\Model\Wiki\WikiNodeGroupRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatus;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatusRepository;
use App\Lodocio\Domain\Model\Wiki\WikiRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddWikiHandler
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

    // —————————————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————————————

    public function go(AddWiki $command): bool
    {
        $project = $this->docProjectRepository->getById($command->getDocProjectId());

        $model = Wiki::make(
            $project,
            $this->wikiRepository->nextIdentity(),
            $command->getName(),
            $command->getCode(),
            '#' . $command->getColor(),
        );
        $model->setSequence($this->wikiRepository->getMaxSequence($project));
        $model->setArtefactId($this->wikiRepository->getNextArtefactId($project));
        $wikiStructure = new WikiStructure();
        $model->setStructure($wikiStructure);
        $id = $this->wikiRepository->save($model);
        $this->entityManager->flush();

        // -- make a start status

        $startStatus = WikiNodeStatus::make(
            $model,
            $this->wikiNodeStatusRepository->nextIdentity(),
            'New',
            'c0c0c0',
            true,
            false,
        );
        $startStatus->setSequence(0);
        $startStatus->setArtefactId(1);
        $this->wikiNodeStatusRepository->save($startStatus);
        $this->entityManager->flush();

        // -- make an end status

        $endStatus = WikiNodeStatus::make(
            $model,
            $this->wikiNodeStatusRepository->nextIdentity(),
            'Final',
            '008000',
            false,
            true,
        );
        $endStatus->setSequence(1);
        $endStatus->setArtefactId(2);
        $this->wikiNodeStatusRepository->save($endStatus);
        $this->entityManager->flush();

        // -- make a first node

        $firstNodeUuid = $this->wikiNodeRepository->nextIdentity();
        $firstNode = WikiNode::make(
            $model,
            $firstNodeUuid,
            $startStatus,
            'my first item',
            0,
            '1'
        );
        $firstNode->setSequence(0);
        $firstNode->setArtefactId(1);
        $this->wikiNodeRepository->save($firstNode);

        // -- make a first group

        $firstGroupUuid = $this->wikiNodeGroupRepository->nextIdentity();
        $firstGroup = WikiNodeGroup::make(
            $model,
            $firstGroupUuid,
            'my first group',
            0,
            '2'
        );
        $firstGroup->setSequence(0);
        $firstGroup->setArtefactId(1);
        $this->wikiNodeGroupRepository->save($firstGroup);

        // create a structure object and register it

        $wikiStructure->addNode(WikiStructureNode::hydrateFromModel($firstNode));
        $wikiStructure->addGroup(WikiStructureGroup::hydrateFromModel($firstGroup));
        $model->setStructure($wikiStructure);

        $id = $this->wikiRepository->save($model);
        $this->entityManager->flush();

        return true;
    }

}
