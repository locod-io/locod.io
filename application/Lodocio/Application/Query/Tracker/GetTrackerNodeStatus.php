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

namespace App\Lodocio\Application\Query\Tracker;

use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerNodeStatusReadModel;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerNodeStatusWorkflow;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerNodeStatusWorkflowRelation;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerNodeStatusWorkflowStatus;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatus;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatusRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRepository;

class GetTrackerNodeStatus
{
    public function __construct(
        protected TrackerRepository $trackerRepository,
        protected TrackerNodeStatusRepository $trackerNodeStatusRepository
    ) {
    }

    public function ById(int $id): TrackerNodeStatusReadModel
    {
        return TrackerNodeStatusReadModel::hydrateFromModel($this->trackerNodeStatusRepository->getById($id));
    }

    public function WorkflowByTracker(int $trackerId): TrackerNodeStatusWorkflow
    {
        $workflow = new TrackerNodeStatusWorkflow();
        $tracker = $this->trackerRepository->getById($trackerId);
        $models = $this->trackerNodeStatusRepository->getByTracker($tracker);

        foreach ($models as $model) {
            $position = new \stdClass();
            if ($model->getX() === 0 && $model->getY() === 0) {
                $position->x = ($model->getSequence() * 120) + 20;
                $position->y = 50;
            } else {
                $position->x = $model->getX();
                $position->y = $model->getY();
            }
            $data = new \stdClass();
            $data->color = $model->getColor();
            if ($model->isStart()) {
                $data->type = 'output';
            } elseif ($model->isFinal()) {
                $data->type = 'input';
            } else {
                $data->type = 'default';
            }
            // -- add status
            $status = new TrackerNodeStatusWorkflowStatus(
                strval($model->getId()),
                $model->getName(),
                'status',
                $data,
                $position
            );
            $workflow->addStatus($status);

            // -- render the relations
            if (!is_null($model->getFlowOut())) {
                foreach ($model->getFlowOut() as $outItemId) {
                    $targetModel = $this->getFlowOutModel($outItemId, $models);
                    if (!is_null($targetModel)) {
                        $style = new \stdClass();
                        $style->strokeWidth = 4;
                        $style->stroke = '#' . $targetModel->getColor();
                        $data = new \stdClass();
                        $data->color = '#' . $targetModel->getColor();
                        $relation = new TrackerNodeStatusWorkflowRelation(
                            'e_' . $model->getId() . '_' . $targetModel->getId(),
                            strval($model->getId()),
                            strval($targetModel->getId()),
                            false,
                            $style,
                            $data
                        );
                        $workflow->addRelation($relation);
                    }
                }
            }
        }

        return $workflow;
    }

    private function getFlowOutModel(int $id, array $models): ?TrackerNodeStatus
    {
        foreach ($models as $model) {
            if ($model->getId() === $id) {
                return $model;
            }
        }
        return null;
    }
}
