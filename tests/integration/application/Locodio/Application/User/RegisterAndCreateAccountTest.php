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

namespace App\Tests\integration\application\Locodio\Application\User;

use App\Locodio\Application\Command\Model\CreateSampleProject\CreateSampleProject;
use App\Locodio\Application\Command\Model\CreateSampleProject\CreateSampleProjectHandler;
use App\Locodio\Application\Command\User\CreateAccount\CreateAccount;
use App\Locodio\Application\Command\User\CreateAccount\CreateAccountHandler;
use App\Locodio\Application\Command\User\Register\Register;
use App\Locodio\Application\Command\User\Register\RegisterHandler;
use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\Model\Association;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserRegistrationLink;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class RegisterAndCreateAccountTest extends DatabaseTestCase
{
    private UserPasswordHasherInterface $passwordEncoderMock;
    private RegisterHandler $registerHandler;

    public function setUp(): void
    {
        parent::setUp();
        $this->passwordEncoderMock = $this->getMockBuilder(UserPasswordHasherInterface::class)->getMock();
        $this->passwordEncoderMock->method('hashPassword')->willReturn('rUQwuf1AioRgxVR');
        $this->registerHandler = new RegisterHandler(
            $this->entityManager->getRepository(UserRegistrationLink::class),
            $this->entityManager->getRepository(User::class),
            $this->passwordEncoderMock
        );
    }

    public function testRegisterUser(): \stdClass
    {
        $command = new Register(
            'organisation',
            'firstname',
            'lastname',
            'registration@test.com',
            'AA7xjIdTVtN4sJi',
            'AA7xjIdTVtN4sJi'
        );
        $result = $this->registerHandler->Register($command);
        $this->entityManager->flush();
        $this->linkUuid = $result->uuid;
        Assert::assertEquals('register_link_created', $result->message);
        Assert::assertTrue(Uuid::isValid($result->uuid));
        return $result;
    }

    /** @depends testRegisterUser */
    public function testLinkRetrieval(\stdClass $message): string
    {
        $linkRepo = $this->entityManager->getRepository(UserRegistrationLink::class);
        $link = $linkRepo->getByUuid(Uuid::fromString($message->uuid));
        Assert::assertEquals('organisation', $link->getOrganisation());
        Assert::assertEquals('registration@test.com', $link->getEmail());
        Assert::assertEquals('firstname', $link->getFirstname());
        Assert::assertEquals('lastname', $link->getLastname());
        Assert::assertEquals($message->uuid, $link->getUuid()->toRfc4122());
        Assert::assertEquals(false, $link->isUsed());
        return $link->getCode();
    }

    /** @depends testLinkRetrieval */
    public function testCreateUser(string $code): \stdClass
    {
        $linkRepo = $this->entityManager->getRepository(UserRegistrationLink::class);
        $link = $linkRepo->getByCode($code);
        Assert::assertEquals('organisation', $link->getOrganisation());
        Assert::assertEquals('registration@test.com', $link->getEmail());
        Assert::assertEquals('firstname', $link->getFirstname());
        Assert::assertEquals('lastname', $link->getLastname());
        Assert::assertEquals('rUQwuf1AioRgxVR', $link->getPassword());
        Assert::assertEquals(false, $link->isUsed());
        $createUserHandler = new CreateAccountHandler(
            $this->entityManager->getRepository(UserRegistrationLink::class),
            $this->entityManager->getRepository(User::class),
            $this->entityManager->getRepository(Organisation::class),
            $this->entityManager->getRepository(Project::class),
        );
        $command = new CreateAccount($code);
        $message = $createUserHandler->CreateAccount($command);
        $this->entityManager->flush();
        Assert::assertEquals('account_created', $message->message);
        Assert::assertTrue(Uuid::isValid($message->projectUuid));

        // -- test link
        $link = $linkRepo->getByCode($code);
        Assert::assertEquals('**', $link->getOrganisation());
        Assert::assertEquals('**', $link->getEmail());
        Assert::assertEquals('**', $link->getFirstname());
        Assert::assertEquals('**', $link->getLastname());
        Assert::assertEquals('', $link->getPassword());
        Assert::assertEquals(true, $link->isUsed());

        // -- test user
        $this->entityManager->getRepository(User::class);
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->getByEmail('registration@test.com');
        Assert::assertEquals('registration@test.com', $user->getEmail());
        Assert::assertEquals('firstname', $user->getFirstname());
        Assert::assertEquals('lastname', $user->getLastname());
        Assert::assertEquals('rUQwuf1AioRgxVR', $user->getPassword());

        return $message;
    }

    /** @depends testCreateUser */
    public function testProjectAndOrganisation(\stdClass $message): \stdClass
    {
        $projectRepo = $this->entityManager->getRepository(Project::class);
        $project = $projectRepo->getByUuid(Uuid::fromString($message->projectUuid));
        Assert::assertEquals('My first project', $project->getName());
        Assert::assertEquals('MFP', $project->getCode());
        Assert::assertEquals('organisation', $project->getOrganisation()->getName());
        return $message;
    }

    /** @depends testProjectAndOrganisation */
    public function testCreateSampleProject(\stdClass $message): void
    {
        $createSampleProjectHandler = new CreateSampleProjectHandler(
            $this->entityManager->getRepository(Project::class),
            $this->entityManager->getRepository(DomainModel::class),
            $this->entityManager->getRepository(Attribute::class),
            $this->entityManager->getRepository(Association::class),
            $this->entityManager->getRepository(Enum::class),
            $this->entityManager->getRepository(EnumOption::class),
            $this->entityManager->getRepository(Query::class),
            $this->entityManager->getRepository(Command::class),
            $this->entityManager->getRepository(Template::class),
        );
        $command = new CreateSampleProject($message->projectUuid);
        $createSampleProjectHandler->go($command);
        $this->entityManager->flush();

        // -- test project again
        $projectRepo = $this->entityManager->getRepository(Project::class);
        $project = $projectRepo->getByUuid(Uuid::fromString($message->projectUuid));
        Assert::assertEquals('My first project', $project->getName());
        Assert::assertEquals('MFP', $project->getCode());
        Assert::assertEquals('organisation', $project->getOrganisation()->getName());

        // -- test the sample project
        $modelRepo = $this->entityManager->getRepository(DomainModel::class);
        $enumRepo = $this->entityManager->getRepository(Enum::class);
        $queryRepo = $this->entityManager->getRepository(Query::class);
        $commandRepo = $this->entityManager->getRepository(Command::class);
        $templateRepo = $this->entityManager->getRepository(Template::class);
        $domainModels = $modelRepo->getByProject($project);
        $enums = $enumRepo->getByProject($project);
        $queries = $queryRepo->getByProject($project);
        $commands = $commandRepo->getByProject($project);
        $templates = $templateRepo->getByProject($project);
        Assert::assertCount(2, $domainModels);
        Assert::assertCount(1, $enums);
        Assert::assertCount(2, $queries);
        Assert::assertCount(2, $commands);
        Assert::assertCount(4, $templates);
    }
}
