<?php

namespace App\Tests\unit\application\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Model\ModelSettings;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class ModelSettingsTest extends TestCase
{
    private ModelSettings $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = ModelSettings::make(
            ModelFactory::makeProject(),
            $this->uuid,
            'domain layer',
            'application layer',
            'infrastructure layer',
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(ModelSettings::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertEquals('domain layer', $this->model->getDomainLayer());
        Assert::assertEquals('application layer', $this->model->getApplicationLayer());
        Assert::assertEquals('infrastructure layer', $this->model->getInfrastructureLayer());
    }

    public function testChange(): void
    {
        $this->model->change(
            'domain changed',
            'application changed',
            'infrastructure changed',
        );
        $this->model->setChecksum();
        Assert::assertEquals('domain changed', $this->model->getDomainLayer());
        Assert::assertEquals('application changed', $this->model->getApplicationLayer());
        Assert::assertEquals('infrastructure changed', $this->model->getInfrastructureLayer());
    }
}
