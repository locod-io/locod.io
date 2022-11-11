<?php

namespace App\Tests\unit\application\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Model\TemplateType;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class TemplateTest extends TestCase
{
    private Template $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = Template::make(
            ModelFactory::makeProject(),
            $this->uuid,
            TemplateType::DOMAIN_MODEL,
            'template name',
            'language',
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(Template::class, $this->model);
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
            'newLanguage',
            'template contents',
        );
        $this->model->setChecksum();
        Assert::assertEquals('new template name', $this->model->getName());
        Assert::assertEquals(TemplateType::COMMAND, $this->model->getType());
        Assert::assertEquals('newLanguage', $this->model->getLanguage());
        Assert::assertEquals('template contents', $this->model->getTemplate());
    }
}
