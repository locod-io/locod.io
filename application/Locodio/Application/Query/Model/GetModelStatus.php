<?php

declare(strict_types=1);

namespace App\Locodio\Application\Query\Model;

use App\Locodio\Application\Query\Model\Readmodel\ModelStatusRM;
use App\Locodio\Application\Query\Model\Readmodel\ModelStatusRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\ModelStatusWorkflow;
use App\Locodio\Application\Query\Model\Readmodel\ModelStatusWorkflowRelation;
use App\Locodio\Application\Query\Model\Readmodel\ModelStatusWorkflowStatus;
use App\Locodio\Domain\Model\Model\DocumentorRepository;
use App\Locodio\Domain\Model\Model\ModelStatus;
use App\Locodio\Domain\Model\Model\ModelStatusRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use Symfony\Component\Uid\Uuid;

class GetModelStatus
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository     $projectRepo,
        protected ModelStatusRepository $modelStatusRepo,
        protected DocumentorRepository  $documentorRepo,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Read functions
    // —————————————————————————————————————————————————————————————————————————

    public function ById(int $id): ModelStatusRM
    {
        return ModelStatusRM::hydrateFromModel($this->modelStatusRepo->getById($id));
    }

    public function ByUuid(string $uuid): ModelStatusRM
    {
        return ModelStatusRM::hydrateFromModel($this->modelStatusRepo->getByUuid(Uuid::fromString($uuid)));
    }

    public function ByProject(int $projectId): ModelStatusRMCollection
    {
        $project = $this->projectRepo->getById($projectId);
        $models = $this->modelStatusRepo->getByProject($project);
        $result = new ModelStatusRMCollection();
        foreach ($models as $model) {
            $statusRM = ModelStatusRM::hydrateFromModel($model, true);
            $statusRM->setUsages($this->documentorRepo->countByModelStatus($statusRM->getId()));
            $result->addItem($statusRM);
        }
        return $result;
    }

    public function WorkflowByProject(int $projectId): ModelStatusWorkflow
    {
        $workflow = new ModelStatusWorkflow();
        $project = $this->projectRepo->getById($projectId);
        $models = $this->modelStatusRepo->getByProject($project);

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
            $status = new ModelStatusWorkflowStatus(
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
                        $relation = new ModelStatusWorkflowRelation(
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

    private function getFlowOutModel(int $id, array $models): ?ModelStatus
    {
        foreach ($models as $model) {
            if ($model->getId() === $id) {
                return $model;
            }
        }
        return null;
    }
}
