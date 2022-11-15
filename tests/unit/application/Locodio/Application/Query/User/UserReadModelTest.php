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

namespace App\Tests\unit\application\Locodio\Application\Query\User;

use App\Locodio\Application\Query\User\Readmodel\AnonymousUserRM;
use App\Locodio\Application\Query\User\Readmodel\UserRM;
use App\Locodio\Application\Query\User\Readmodel\UserRMCollection;
use App\Locodio\Domain\Model\User\User;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class UserReadModelTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = ModelFactory::makeUser();
        $this->user->identify(1, Uuid::fromString('fd16d030-e2d0-4997-b229-298b66bafd99'));
        $this->user->change('firstname', 'lastname', 'color');
    }

    public function testReadModel(): void
    {
        $readModel = UserRM::hydrateFromModel($this->user);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals("fd16d030-e2d0-4997-b229-298b66bafd99", $result->uuid);
        Assert::assertEquals('user@test.com', $result->email);
        Assert::assertEquals("firstname", $result->firstname);
        Assert::assertEquals("lastname", $result->lastname);
        Assert::assertEquals("color", $result->color);
        Assert::assertEquals("FL", $result->initials);
    }

    public function testAnonymousReadModel(): void
    {
        $readModel = AnonymousUserRM::hydrateFromModel($this->user);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals("color", $result->color);
        Assert::assertEquals("FL", $result->initials);
    }

    public function testReadModelCollection(): void
    {
        $readModel = UserRM::hydrateFromModel($this->user);
        $collection = new UserRMCollection();
        $collection->addItem($readModel);
        $result = json_decode(json_encode($collection));
        Assert::assertTrue(is_array($result->collection));
    }
}
