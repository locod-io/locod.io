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

namespace App\Lodocio\Application\Query\Tracker\ReadModel;

use App\Lodocio\Domain\Model\Tracker\Tracker;

class TrackerReadModelFactory
{
    private TrackerReadModel $trackerReadmodel;
    private TrackerNodeReadModelCollection $trackerNodes;
    private TrackerNodeGroupReadModelCollection $trackerGroups;

    private TrackerFlattenedReadModel $flattenedReadModel;

    public function __construct(protected Tracker $tracker)
    {
        $this->trackerNodes = new TrackerNodeReadModelCollection();
        $this->trackerGroups = new TrackerNodeGroupReadModelCollection();

        foreach ($this->tracker->getTrackerNodes() as $elementModel) {
            $this->trackerNodes->addItem(TrackerNodeReadModel::hydrateFromModel($elementModel));
        }
        foreach ($this->tracker->getTrackerGroups() as $elementGroup) {
            $this->trackerGroups->addItem(TrackerNodeGroupReadModel::hydrateFromModel($elementGroup));
        }

    }

    public function getCompleteReadModel(): TrackerReadModel
    {
        $trackerReadModel = TrackerReadModel::hydrateFromModel($this->tracker, true);
        $nodes = new TrackerNodeReadModelCollection();
        foreach ($this->tracker->getStructure()['nodes'] as $node) {
            $nodeResult = $this->getNodeByUuid($node['uuid']);
            if(false === is_null($nodeResult)) {
                $nodes->addItem($nodeResult);
            }
        }
        $groups = new TrackerNodeGroupReadModelCollection();
        foreach ($this->tracker->getStructure()['groups'] as $group) {
            $groupResult = $this->getGroupByUuid($group);
            if(false === is_null($groupResult)) {
                $groups->addItem($groupResult);
            }
        }
        $trackerReadModel->setNodes($nodes);
        $trackerReadModel->setGroups($groups);
        $this->trackerReadmodel = $trackerReadModel;
        return $this->trackerReadmodel;
    }

    public function getFlattenedReadModel(): TrackerFlattenedReadModel
    {
        $this->flattenedReadModel = TrackerFlattenedReadModel::hydrateFromModel($this->tracker);
        foreach ($this->tracker->getStructure()['nodes'] as $node) {
            $this->flattenedReadModel->addElement($this->getNodeByUuid($node['uuid']));
        }
        foreach ($this->tracker->getStructure()['groups'] as $group) {
            $this->getGroupByUuidFlattened($group);
        }

        return $this->flattenedReadModel;
    }

    // —————————————————————————————————————————————————————————————————————————
    // private functions
    // —————————————————————————————————————————————————————————————————————————

    private function getNodeByUuid($uuid): ?TrackerNodeReadModel
    {
        foreach ($this->trackerNodes->getCollection() as $element) {
            if ($element->getUuid() === $uuid) {
                return $element;
            }
        }
        return null;
    }


    private function getGroupByUuid($srcGroup): ?TrackerNodeGroupReadModel
    {
        foreach ($this->trackerGroups->getCollection() as $group) {
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
        foreach ($this->trackerGroups->getCollection() as $group) {
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
