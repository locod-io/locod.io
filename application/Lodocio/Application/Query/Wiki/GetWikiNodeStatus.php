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

namespace App\Lodocio\Application\Query\Wiki;

use App\Lodocio\Application\Query\Wiki\ReadModel\WikiNodeStatusReadModel;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiNodeStatusWorkflow;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiNodeStatusWorkflowRelation;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiNodeStatusWorkflowStatus;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatus;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatusRepository;
use App\Lodocio\Domain\Model\Wiki\WikiRepository;

class GetWikiNodeStatus
{
    public function __construct(
        protected WikiRepository $wikiRepository,
        protected WikiNodeStatusRepository $wikiNodeStatusRepository
    ) {
    }

    public function ById(int $id): WikiNodeStatusReadModel
    {
        return WikiNodeStatusReadModel::hydrateFromModel($this->wikiNodeStatusRepository->getById($id));
    }

    public function WorkflowByWiki(int $wikiId): WikiNodeStatusWorkflow
    {
        $workflow = new WikiNodeStatusWorkflow();
        $wiki = $this->wikiRepository->getById($wikiId);
        $models = $this->wikiNodeStatusRepository->getByWiki($wiki);

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
            $status = new WikiNodeStatusWorkflowStatus(
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
                        $relation = new WikiNodeStatusWorkflowRelation(
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

    private function getFlowOutModel(int $id, array $models): ?WikiNodeStatus
    {
        foreach ($models as $model) {
            if ($model->getId() === $id) {
                return $model;
            }
        }
        return null;
    }
}
