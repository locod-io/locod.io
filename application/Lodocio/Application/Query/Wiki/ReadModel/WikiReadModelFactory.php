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

namespace App\Lodocio\Application\Query\Wiki\ReadModel;

use App\Lodocio\Domain\Model\Wiki\Wiki;

class WikiReadModelFactory
{
    private WikiReadModel $wikiReadmodel;
    private WikiNodeReadModelCollection $wikiNodes;
    private WikiNodeGroupReadModelCollection $wikiGroups;

    private WikiFlattenedReadModel $flattenedReadModel;

    public function __construct(protected Wiki $wiki)
    {
        $this->wikiNodes = new WikiNodeReadModelCollection();
        $this->wikiGroups = new WikiNodeGroupReadModelCollection();

        foreach ($this->wiki->getWikiNodes() as $elementModel) {
            $this->wikiNodes->addItem(WikiNodeReadModel::hydrateFromModel($elementModel));
        }
        foreach ($this->wiki->getWikiGroups() as $elementGroup) {
            $this->wikiGroups->addItem(WikiNodeGroupReadModel::hydrateFromModel($elementGroup));
        }

    }

    public function getCompleteReadModel(): WikiReadModel
    {
        $wikiReadModel = WikiReadModel::hydrateFromModel($this->wiki, true);
        $nodes = new WikiNodeReadModelCollection();
        foreach ($this->wiki->getStructure()['nodes'] as $node) {
            $nodeResult = $this->getNodeByUuid($node['uuid']);
            if (false === is_null($nodeResult)) {
                $nodes->addItem($nodeResult);
            }
        }
        $groups = new WikiNodeGroupReadModelCollection();
        foreach ($this->wiki->getStructure()['groups'] as $group) {
            $groupResult = $this->getGroupByUuid($group);
            if (false === is_null($groupResult)) {
                $groups->addItem($groupResult);
            }
        }
        $wikiReadModel->setNodes($nodes);
        $wikiReadModel->setGroups($groups);
        $this->wikiReadmodel = $wikiReadModel;
        return $this->wikiReadmodel;
    }

    public function getFlattenedReadModel(): WikiFlattenedReadModel
    {
        $this->flattenedReadModel = WikiFlattenedReadModel::hydrateFromModel($this->wiki);
        foreach ($this->wiki->getStructure()['nodes'] as $node) {
            $this->flattenedReadModel->addElement($this->getNodeByUuid($node['uuid']));
        }
        foreach ($this->wiki->getStructure()['groups'] as $group) {
            $this->getGroupByUuidFlattened($group);
        }

        return $this->flattenedReadModel;
    }

    // —————————————————————————————————————————————————————————————————————————
    // private functions
    // —————————————————————————————————————————————————————————————————————————

    private function getNodeByUuid($uuid): ?WikiNodeReadModel
    {
        foreach ($this->wikiNodes->getCollection() as $element) {
            if ($element->getUuid() === $uuid) {
                return $element;
            }
        }
        return null;
    }


    private function getGroupByUuid($srcGroup): ?WikiNodeGroupReadModel
    {
        foreach ($this->wikiGroups->getCollection() as $group) {
            if ($group->getUuid() === $srcGroup['uuid']) {
                // -- set the nodes
                foreach ($srcGroup['nodes'] as $node) {
                    $group->addNode($this->getNodeByUuid($node['uuid']));
                }
                // -- set the groups
                foreach ($srcGroup['groups'] as $secondSrcGroup) {
                    $group->addGroup($this->getGroupByUuid($secondSrcGroup));
                }
                // -- return the group
                return $group;
            }
        }
        return null;
    }

    private function getGroupByUuidFlattened($srcGroup): void
    {
        foreach ($this->wikiGroups->getCollection() as $group) {
            if ($group->getUuid() === $srcGroup['uuid']) {
                // -- set the group
                $this->flattenedReadModel->addElement($group);

                // -- set the nodes
                foreach ($srcGroup['nodes'] as $node) {
                    $this->flattenedReadModel->addElement($this->getNodeByUuid($node['uuid']));
                }
                // -- set the group
                foreach ($srcGroup['groups'] as $secondSrcGroup) {
                    $this->getGroupByUuidFlattened($secondSrcGroup);
                }
            }
        }
    }

}
