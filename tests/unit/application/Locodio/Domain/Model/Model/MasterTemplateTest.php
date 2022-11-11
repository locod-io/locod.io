<?php

namespace App\Tests\unit\application\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\TemplateType;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class MasterTemplateTest extends TestCase
{
    private MasterTemplate $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = MasterTemplate::make(
            ModelFactory::makeUser(),
            $this->uuid,
            TemplateType::DOMAIN_MODEL,
            'template name',
            'language',
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(MasterTemplate::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertEquals('template name', $this->model->getName());
        Assert::assertEquals(TemplateType::DOMAIN_MODEL, $this->model->getType());
        Assert::assertEquals('language', $this->model->getLanguage());
    }

    public function testChange(): void
    {
        $this->model->change(
            TemplateType::COMMAND,
            'new template name',
            'language',
            'template content',
            false,
            'some description',
            ['tag1', 'tag2'],
        );
        $this->model->setChecksum();
        Assert::assertEquals('new template name', $this->model->getName());
        Assert::assertEquals(TemplateType::COMMAND, $this->model->getType());
        Assert::assertEquals('language', $this->model->getLanguage());
        Assert::assertEquals('template content', $this->model->getTemplate());
        Assert::assertEquals(false, $this->model->isPublic());
        Assert::assertEquals('some description', $this->model->getDescription());
        Assert::assertEquals(['tag1', 'tag2'], $this->model->getTags());
    }
}
