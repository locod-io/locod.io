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

namespace App\Tests\integration\application\Locodio\Domain\Model\User;

use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserRepository;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class userTest extends DatabaseTestCase
{
    private User $user;
    private Uuid $uuid;
    private ?UserRepository $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function testSaveAdminUser()
    {
        $this->uuid = Uuid::v4();
        $this->user = User::make(
            $this->uuid,
            'admin@test.com',
            'Firstname',
            'Lastname',
            ['ROLE_ADMIN'],
        );

        // -- make
        $this->user->setPassword('admin');
        $this->userRepository->save($this->user);
        $this->entityManager->flush();
        $user = $this->userRepository->getByUuid($this->uuid);
        Assert::assertEquals('Firstname', $user->getFirstname());
        Assert::assertEquals('Lastname', $user->getLastname());
        Assert::assertEquals('admin@test.com', $user->getEmail());

        // -- change
        $user->change("Changed Firstname", "Changed Lastname", "color");
        $this->userRepository->save($this->user);
        $this->entityManager->flush();
        $user = $this->userRepository->getByUuid($this->uuid);
        Assert::assertEquals('Changed Firstname', $user->getFirstname());
        Assert::assertEquals('Changed Lastname', $user->getLastname());
        Assert::assertEquals('color', $user->getColor());
        Assert::assertEquals('admin@test.com', $user->getEmail());
    }
}
