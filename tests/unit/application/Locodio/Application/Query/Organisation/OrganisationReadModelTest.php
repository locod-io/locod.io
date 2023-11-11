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

namespace App\Tests\unit\application\Locodio\Application\Query\Organisation;

use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRM;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRMCollection;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class OrganisationReadModelTest extends TestCase
{
    private Organisation $organisation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organisation = ModelFactory::makeOrganisation();
        $this->organisation->identify(1, Uuid::fromString('dd11da44-aeea-46fa-ba69-03c874608af2'));
        $this->organisation->change('organisation', 'ORG', 'color', 'some-key','some-slug');
    }

    public function testReadModel(): void
    {
        $readModel = OrganisationRM::hydrateFromModel($this->organisation);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals('dd11da44-aeea-46fa-ba69-03c874608af2', $result->uuid);
        Assert::assertEquals('organisation', $result->name);
        Assert::assertEquals('ORG', $result->code);
        Assert::assertEquals('color', $result->color);
        Assert::assertEquals('some-slug', $result->slug);
        Assert::assertNotEquals('some-key', $result->linearApiKey);
    }

    public function testReadModelCollection(): void
    {
        $readModel = OrganisationRM::hydrateFromModel($this->organisation);
        $collection = new OrganisationRMCollection();
        $collection->addItem($readModel);
        $result = json_decode(json_encode($collection));
        Assert::assertTrue(is_array($result->collection));
    }
}
