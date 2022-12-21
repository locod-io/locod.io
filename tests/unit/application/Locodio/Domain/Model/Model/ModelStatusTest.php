<?php

namespace App\Tests\unit\application\Locodio\Domain\Model\Model;

use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\TestCase;
use App\Locodio\Domain\Model\Model\ModelStatus;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

final class ModelStatusTest extends TestCase
{
    private ModelStatus $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = ModelStatus::make(
            ModelFactory::makeProject(),
            $this->uuid,
            'name',
            'color',
            true,
            false,
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(ModelStatus::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertEquals('name', $this->model->getName());
        Assert::assertEquals('color', $this->model->getColor());
        Assert::assertEquals(true, $this->model->isStart());
        Assert::assertEquals(false, $this->model->isFinal());
        Assert::assertEquals(0, $this->model->getX());
        Assert::assertEquals(0, $this->model->getY());
    }

    public function testChange(): void
    {
        $this->model->change(
            'name changed',
            'color changed',
            false,
            true
        );
        $this->model->setChecksum();
        Assert::assertEquals('name changed', $this->model->getName());
        Assert::assertEquals('color changed', $this->model->getColor());
        Assert::assertEquals(false, $this->model->isStart());
        Assert::assertEquals(true, $this->model->isFinal());
    }

    public function testDeFinalize(): void
    {
        $this->model->deFinalize();
        $this->model->setChecksum();
        Assert::assertEquals(false, $this->model->isFinal());
    }

    public function testDeStarterize(): void
    {
        $this->model->deStarterize();
        $this->model->setChecksum();
        Assert::assertEquals(false, $this->model->isStart());
    }

    public function testSetWorkflow(): void
    {
        $position = new \stdClass();
        $position->x = 10;
        $position->y = 20;
        $this->model->setWorkflow($position, [1,2], [3,4]);
        Assert::assertEquals(10, $this->model->getX());
        Assert::assertEquals(20, $this->model->getY());
        Assert::assertEquals([1,2], $this->model->getFlowIn());
        Assert::assertEquals([3,4], $this->model->getFlowOut());
    }
}
