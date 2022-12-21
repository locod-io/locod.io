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

use App\Locodio\Application\Command\User\ChangePassword\ChangePassword;
use App\Locodio\Application\Command\User\ChangePassword\ChangePasswordHandler;
use App\Locodio\Domain\Model\User\User;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordTest extends DatabaseTestCase
{
    private UserPasswordHasherInterface $passwordEncoderMock;
    private DatabaseModelFactory $modelFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->passwordEncoderMock = $this->getMockBuilder(UserPasswordHasherInterface::class)->getMock();
        $this->passwordEncoderMock->method('hashPassword')->willReturn('rUQwuf1AioRgxVX');
        $this->modelFactory = new DatabaseModelFactory($this->entityManager);
    }

    public function testResetPassword(): void
    {
        $user = $this->modelFactory->makeUser('resetpassword@test.com');
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->getByEmail('resetpassword@test.com');
        $jsonCommand = new \stdClass();
        $jsonCommand->userId = $user->getId();
        $jsonCommand->password1 = 'UMPi4SZmc5nJdWs';
        $jsonCommand->password2 = 'UMPi4SZmc5nJdWs';
        $command = ChangePassword::hydrateFromJson($jsonCommand);
        $resetPasswordHandler = new ChangePasswordHandler(
            $this->entityManager->getRepository(User::class),
            $this->passwordEncoderMock
        );
        $resetPasswordHandler->go($command);
        $this->entityManager->flush();
        $user = $userRepo->getByEmail('resetpassword@test.com');
        Assert::assertEquals('rUQwuf1AioRgxVX', $user->getPassword());
    }
}
