<?php

namespace App\Tests\unit\application\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DocumentorType;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class DocumentorTest extends TestCase
{
    private Documentor $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = Documentor::make(
            $this->uuid,
            DocumentorType::MODULE,
            'description',
            ModelFactory::makeModelStatus(),
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(Documentor::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertEquals(DocumentorType::MODULE, $this->model->getType());
        Assert::assertEquals('description', $this->model->getDescription());
    }

    public function testChange(): void
    {
        $this->model->change(
            'description changed',
            ModelFactory::makeModelStatus()
        );
        $this->model->setChecksum();
        Assert::assertEquals('description changed', $this->model->getDescription());
    }

    public function testImage(): void
    {
        $this->model->setImage('image');
        $this->model->setChecksum();
        Assert::assertEquals('image', $this->model->getImage());
    }

    public function testOverview(): void
    {
        $overview = new \stdClass();
        $overview->test = "test";
        $this->model->setOverview($overview);
        $this->model->setChecksum();
        Assert::assertEquals('test', $this->model->getOverview()->test);
    }
}
