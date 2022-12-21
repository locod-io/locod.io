<?php

namespace App\Tests\unit\application\Locodio\Application\Command\Model;

use App\Locodio\Application\Command\Model\SaveModelStatusWorkflow\SaveModelStatusWorkflow;
use App\Locodio\Application\Command\Model\SaveModelStatusWorkflow\WorkflowItem;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class SaveModelStatusWorkflowTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->workflow = [];
        $item = new \stdClass();
        $item->id = 'id';
        $item->label = 'label';
        $position = new \stdClass();
        $position->x = 10;
        $position->y = 20;
        $item->position = $position;
        $item->flowIn = [1, 2];
        $item->flowOut = [3, 4];
        $jsonCommand->workflow[] = $item;
        $command = SaveModelStatusWorkflow::hydrateFromJson($jsonCommand);
        /** @var WorkflowItem $firstItem */
        $firstItem = $command->getWorkflow()[0];
        Assert::assertEquals('id', $firstItem->getId());
        Assert::assertEquals('label', $firstItem->getLabel());
        Assert::assertEquals(10, $firstItem->getPosition()->x);
        Assert::assertEquals(20, $firstItem->getPosition()->y);
        Assert::assertEquals([1, 2], $firstItem->getFlowIn());
        Assert::assertEquals([3, 4], $firstItem->getFlowOut());
    }
}
