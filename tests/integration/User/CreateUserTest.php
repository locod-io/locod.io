<?php

namespace App\Tests\integration\User;

use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserRepository;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class CreateUserTest extends DatabaseTestCase
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
        $this->user->setPassword('admin');
        $id = $this->userRepository->save($this->user);
        $this->entityManager->flush();
        $user = $this->userRepository->getByUuid($this->uuid);

        Assert::assertEquals('Firstname', $user->getFirstname());
        Assert::assertEquals('Lastname', $user->getLastname());
        Assert::assertEquals('admin@test.com', $user->getEmail());
    }
}
