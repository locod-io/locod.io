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

use App\Locodio\Application\Query\Model\Readmodel\MasterTemplateRM;
use App\Locodio\Application\Query\Model\Readmodel\MasterTemplateRMCollection;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\TemplateType;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class MasterTemplateReadModelTest extends TestCase
{
    private MasterTemplate $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = MasterTemplate::make(
            ModelFactory::makeUser(),
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
            false,
            'description',
            ['tag1', 'tag2']
        );
    }

    public function testReadModel(): void
    {
        $readModel = MasterTemplateRM::hydrateFromModel($this->model, true);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals('dd11da44-aeea-46fa-ba69-03c874608af2', $result->uuid);
        Assert::assertEquals('name', $result->name);
        Assert::assertEquals('language', $result->language);
        Assert::assertEquals('template', $result->template);
        Assert::assertEquals(false, $result->isPublic);
        Assert::assertEquals('description', $result->description);
        Assert::assertEquals(['tag1', 'tag2'], $result->tags);
    }

    public function testReadModelCollection(): void
    {
        $readModel = MasterTemplateRM::hydrateFromModel($this->model);
        $collection = new MasterTemplateRMCollection();
        $collection->addItem($readModel);
        $result = json_decode(json_encode($collection));
        Assert::assertTrue(is_array($result->collection));
    }
}
