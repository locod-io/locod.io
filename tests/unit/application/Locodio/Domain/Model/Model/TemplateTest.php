<?php

/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\unit\application\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Model\TemplateType;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
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

    public function testChangeTemplate(): void
    {
        $this->model->changeTemplateContents('changed template content');
        $this->model->setChecksum();
        Assert::assertEquals('changed template content', $this->model->getTemplate());
    }
}
