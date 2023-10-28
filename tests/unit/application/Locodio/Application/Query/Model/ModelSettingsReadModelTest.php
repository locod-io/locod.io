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

use App\Locodio\Application\Query\Model\Readmodel\ModelSettingsRM;
use App\Locodio\Domain\Model\Model\ModelSettings;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ModelSettingsReadModelTest extends TestCase
{
    private ModelSettings $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = ModelSettings::make(
            ModelFactory::makeProject(),
            Uuid::v4(),
            'domain',
            'application',
            'infrastructure',
            ['team1', 'team2']
        );
        $this->model->identify(1, Uuid::fromString('dd11da44-aeea-46fa-ba69-03c874608af2'));
    }

    public function testReadModel(): void
    {
        $readModel = ModelSettingsRM::hydrateFromModel($this->model);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals('dd11da44-aeea-46fa-ba69-03c874608af2', $result->uuid);
        Assert::assertEquals('domain', $result->domainLayer);
        Assert::assertEquals('application', $result->applicationLayer);
        Assert::assertEquals('infrastructure', $result->infrastructureLayer);
        Assert::assertEquals(['team1', 'team2'], $result->teams);
    }
}
