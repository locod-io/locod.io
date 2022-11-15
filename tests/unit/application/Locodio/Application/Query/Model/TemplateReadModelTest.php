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

namespace App\Tests\unit\application\Locodio\Application\Query\Model;

use App\Locodio\Application\Query\Model\Readmodel\TemplateRM;
use App\Locodio\Application\Query\Model\Readmodel\TemplateRMCollection;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Model\TemplateType;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class TemplateReadModelTest extends TestCase
{
    private Template $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = Template::make(
            ModelFactory::makeProject(),
            Uuid::v4(),
            TemplateType::COMMAND,
            'name',
            'language'
        );
        $this->model->identify(1, Uuid::fromString('dd11da44-aeea-46fa-ba69-03c874608af2'));
        $this->model->change(
            TemplateType::COMMAND,
            'name',
            'language',
            'template',
        );
        $this->model->importMasterTemplate(ModelFactory::makeMasterTemplate());
    }

    public function testReadModel(): void
    {
        $readModel = TemplateRM::hydrateFromModel($this->model, true);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals('dd11da44-aeea-46fa-ba69-03c874608af2', $result->uuid);
        Assert::assertEquals('name', $result->name);
        Assert::assertEquals(TemplateType::COMMAND->value, $result->type);
        Assert::assertEquals('language', $result->language);
        Assert::assertEquals('template', $result->template);
    }

    public function testReadModelCollection(): void
    {
        $readModel = TemplateRM::hydrateFromModel($this->model);
        $collection = new TemplateRMCollection();
        $collection->addItem($readModel);
        $result = json_decode(json_encode($collection));
        Assert::assertTrue(is_array($result->collection));
    }
}
