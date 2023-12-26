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

use App\Locodio\Application\Command\User\CreateAccountFromGitHub\CreateAccountFromGitHub;
use App\Locodio\Application\Command\User\CreateAccountFromGitHub\CreateAccountFromGitHubHandler;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\OrganisationUser;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserRole;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserFromGitHubTest extends DatabaseTestCase
{

    private UserPasswordHasherInterface $passwordEncoderMock;

    private CreateAccountFromGitHubHandler $createAccountFromGitHubHandler;

    public function setUp(): void
    {
        parent::setUp();
        $this->passwordEncoderMock = $this->getMockBuilder(UserPasswordHasherInterface::class)->getMock();
        $this->passwordEncoderMock->method('hashPassword')->willReturn('rUQwuf1AioRgxVR');
        $this->createAccountFromGitHubHandler = new CreateAccountFromGitHubHandler(
            userRepo: $this->entityManager->getRepository(User::class),
            organisationRepo: $this->entityManager->getRepository(Organisation::class),
            projectRepo: $this->entityManager->getRepository(Project::class),
            organisationUserRepository: $this->entityManager->getRepository(OrganisationUser::class),
            passwordEncoder: $this->passwordEncoderMock
        );
    }

    public function testRegisterUserFromGitHub(): bool
    {
        $command = CreateAccountFromGitHub::make(
            name: 'Firstname Lastname',
            email: 'firstname.lastname@test-github.com',
            company: 'My company',
        );
        $result = $this->createAccountFromGitHubHandler->register($command);
        $this->entityManager->flush();
        Assert::assertTrue($result);
        return $result;
    }

    /** @depends testRegisterUserFromGitHub */
    public function testAccount(bool $result): void
    {
        // -- set some repo's
        $userRepo = $this->entityManager->getRepository(User::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);
        $organisationRepo = $this->entityManager->getRepository(Organisation::class);
        $organisationUserRepo = $this->entityManager->getRepository(OrganisationUser::class);

        // -- test the user
        $user = $userRepo->getByEmail('firstname.lastname@test-github.com');
        Assert::assertInstanceOf(User::class, $user);
        Assert::assertEquals('Firstname', $user->getFirstname());
        Assert::assertEquals('Lastname', $user->getLastname());
        Assert::assertEquals('firstname.lastname@test-github.com', $user->getEmail());
        Assert::assertEquals('github', $user->getProvider());
        Assert::assertCount(4, $user->getRoles());
        Assert::assertContains('ROLE_USER', $user->getRoles());
        Assert::assertContains(UserRole::ROLE_ORGANISATION_USER->value, $user->getRoles());
        Assert::assertContains(UserRole::ROLE_ORGANISATION_VIEWER->value, $user->getRoles());
        Assert::assertContains(UserRole::ROLE_ORGANISATION_ADMIN->value, $user->getRoles());

        // -- test the organisation
        $organisation = $organisationRepo->getByUser($user)[0];
        Assert::assertInstanceOf(Organisation::class, $organisation);
        Assert::assertEquals('My company', $organisation->getName());

        // -- test the project
        $project = $projectRepo->getByOrganisation($organisation)[0];
        Assert::assertInstanceOf(Project::class, $project);
        Assert::assertEquals('My first project', $project->getName());

        // -- test the organisation user
        $organisationUser = $organisationUserRepo->findByUserAndOrganisation($user, $organisation);
        Assert::assertInstanceOf(OrganisationUser::class, $organisationUser);
        Assert::assertEquals($user->getId(), $organisationUser->getUser()->getId());
        Assert::assertEquals($organisation->getId(), $organisationUser->getOrganisation()->getId());
        Assert::assertCount(4, $user->getRoles());
        Assert::assertContains('ROLE_USER', $user->getRoles());
        Assert::assertContains(UserRole::ROLE_ORGANISATION_USER->value, $organisationUser->getRoles());
        Assert::assertContains(UserRole::ROLE_ORGANISATION_VIEWER->value, $organisationUser->getRoles());
        Assert::assertContains(UserRole::ROLE_ORGANISATION_ADMIN->value, $organisationUser->getRoles());
    }
}