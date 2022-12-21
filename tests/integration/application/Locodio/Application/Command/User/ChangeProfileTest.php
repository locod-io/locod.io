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

namespace App\Tests\integration\application\Locodio\Application\Command\User;

use App\Locodio\Application\Command\User\ChangeProfile\ChangeProfile;
use App\Locodio\Application\Command\User\ChangeProfile\ChangeProfileHandler;
use App\Locodio\Domain\Model\User\User;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;

class ChangeProfileTest extends DatabaseTestCase
{
    private DatabaseModelFactory $modelFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->modelFactory = new DatabaseModelFactory($this->entityManager);
    }

    public function testChangeProfile(): void
    {
        $user = $this->modelFactory->makeUser('changeprofile@test.com');
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->getByEmail('changeprofile@test.com');
        $jsonCommand = new \stdClass();
        $jsonCommand->userId = $user->getId();
        $jsonCommand->firstname = 'new firstname';
        $jsonCommand->lastname = 'new lastname';
        $jsonCommand->color = 'color';
        $command = ChangeProfile::hydrateFromJson($jsonCommand);
        $commandHandler = new ChangeProfileHandler($userRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        // -- test user result
        $user = $userRepo->getByEmail('changeprofile@test.com');
        Assert::assertEquals('new firstname', $user->getFirstname());
        Assert::assertEquals('new lastname', $user->getLastname());
        Assert::assertEquals('#color', $user->getColor());
    }
}
