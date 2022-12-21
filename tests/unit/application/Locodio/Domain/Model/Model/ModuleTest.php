<?php

namespace App\Tests\unit\application\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Model\DocumentorType;
use App\Locodio\Domain\Model\Model\Module;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class ModuleTest extends TestCase
{
    private Module $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = Module::make(
            ModelFactory::makeProject(),
            $this->uuid,
            'name',
            'namespace',
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(Module::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertEquals('name', $this->model->getName());
        Assert::assertEquals('namespace', $this->model->getNamespace());
    }

    public function testChange(): void
    {
        $this->model->change(
            'name changed',
            'namespace changed',
        );
        $this->model->setChecksum();
        Assert::assertEquals('name changed', $this->model->getName());
        Assert::assertEquals('namespace changed', $this->model->getNamespace());
    }

    public function testDocument(): void
    {
        $this->model->document(ModelFactory::makeDocumentor(DocumentorType::MODULE));
        $this->model->setChecksum();
        Assert::assertEquals(DocumentorType::MODULE, $this->model->getDocumentor()->getType());
        Assert::assertEquals('documentor', $this->model->getDocumentor()->getDescription());
    }
}
