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

use App\Locodio\Application\Command\User\ForgotPassword\ForgotPassword;
use App\Locodio\Application\Command\User\ForgotPassword\ForgotPasswordHandler;
use App\Locodio\Application\Command\User\ResetPassword\ResetPasswordHandler;
use App\Locodio\Application\Command\User\ResetPassword\ResetPasswordHash;
use App\Locodio\Domain\Model\User\PasswordResetLink;
use App\Locodio\Domain\Model\User\User;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class ForgotPasswordAndResetTest extends DatabaseTestCase
{
    private UserPasswordHasherInterface $passwordEncoderMock;
    private DatabaseModelFactory $modelFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->passwordEncoderMock = $this->getMockBuilder(UserPasswordHasherInterface::class)->getMock();
        $this->passwordEncoderMock->method('hashPassword')->willReturn('rUQwuf1AioRgxVR');
        $this->modelFactory = new DatabaseModelFactory($this->entityManager);
    }

    public function testGetResetLink(): \stdClass
    {
        $user = $this->modelFactory->makeUser('forgot@test.com');
        $jsonCommand = new \stdClass();
        $jsonCommand->email = 'forgot@test.com';
        $command = ForgotPassword::hydrateFromJson($jsonCommand);
        $forgotPasswordHandler = new ForgotPasswordHandler(
            $this->entityManager->getRepository(User::class),
            $this->entityManager->getRepository(PasswordResetLink::class)
        );
        $message = $forgotPasswordHandler->ForgotPassword($command);
        $this->entityManager->flush();
        Assert::assertEquals('reset_link_sent', $message->message);
        Assert::assertTrue(Uuid::isValid($message->uuid));
        return $message;
    }

    /** @depends testGetResetLink */
    public function testResetLink(\stdClass $message): string
    {
        $linkRepo = $this->entityManager->getRepository(PasswordResetLink::class);
        $link = $linkRepo->getByUuid(Uuid::fromString($message->uuid));
        Assert::assertEquals(false, $link->isUsed());
        Assert::assertEquals(true, $link->isActive());
        Assert::assertEquals('forgot@test.com', $link->getUser()->getEmail());
        return $link->getCode();
    }

    /** @depends testResetLink */
    public function testResetPassword(string $code): void
    {
        $linkRepo = $this->entityManager->getRepository(PasswordResetLink::class);
        $link = $linkRepo->getByCode($code);
        Assert::assertEquals(false, $link->isUsed());
        Assert::assertEquals(true, $link->isActive());
        Assert::assertEquals('forgot@test.com', $link->getUser()->getEmail());

        // -- reset password
        $command = new ResetPasswordHash($code, 'uKVsCYOTINUVizY');
        $resetPasswordHandler = new ResetPasswordHandler(
            $this->passwordEncoderMock,
            $this->entityManager->getRepository(User::class),
            $this->entityManager->getRepository(PasswordResetLink::class)
        );
        $resetPasswordHandler->UserResetPasswordHash($command);
        $this->entityManager->flush();

        // -- test the user
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->getByEmail('forgot@test.com');
        Assert::assertEquals('rUQwuf1AioRgxVR', $user->getPassword());

        // -- test the link
        $link = $linkRepo->getByCode($code);
        Assert::assertEquals(true, $link->isUsed());
        Assert::assertEquals(false, $link->isActive());
    }
}
