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

namespace App\Tests\integration\application\Locodio\Security;

use App\Locodio\Application\Security\BasePermissionService;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\User;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class BasePermissionServiceTest extends DatabaseTestCase
{
    protected const NOT_ALLOWED_DATA = 'Action not allowed for this user.';

    public function setUp(): void
    {
        parent::setUp();
    }

    // -- create project stack ---------------------------------------------------------------

    private function createProjectStack(string $email): Project
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $organisationRepo = $this->entityManager->getRepository(Organisation::class);

        $project = $modelFactory->makeProject(Uuid::v4());
        $user = $modelFactory->makeUser($email);
        $project->getOrganisation()->addUser($user);
        $organisationRepo->save($project->getOrganisation());
        $this->entityManager->flush();

        return $project;
    }

    // -- tests ------------------------------------------------------------------------------

    public function testBasePermissionServiceWithUser()
    {
        $email = 'testBaseSecurityService@test.com';
        $project = $this->createProjectStack($email);
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->getByEmail($email);
        $user->addOrganisation($project->getOrganisation());

        $permissionService = new BasePermissionService($user, $this->entityManager);
        $permissionService->CheckUserId($user->getId());
        $permissionService->CheckOrganisationId($project->getOrganisation()->getId());
        $permissionService->CheckProjectId($project->getId());
        $permissionService->CheckRole(['ROLE_USER']);

        Assert::assertInstanceOf(User::class, $user);
        Assert::assertInstanceOf(Project::class, $project);
        Assert::assertInstanceOf(Organisation::class, $project->getOrganisation());
    }

    public function testBasePermissionServiceWithOtherUser_UserId()
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $project = $this->createProjectStack('testBaseSecurityService_1@test.com');
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->getByEmail('testBaseSecurityService_1@test.com');
        $user->addOrganisation($project->getOrganisation());

        $otherUser = $modelFactory->makeUser('testBaseSecurityServiceOther_1@test.com');

        $permissionService = new BasePermissionService($otherUser, $this->entityManager);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $userId = $project->getOrganisation()->getUsers()[0]->getId();
        $permissionService->CheckUserId($userId);
    }

    public function testBasePermissionServiceWithOtherUser_OrganisationId()
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $project = $this->createProjectStack('testBaseSecurityService_2@test.com');
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->getByEmail('testBaseSecurityService_2@test.com');
        $user->addOrganisation($project->getOrganisation());

        $otherUser = $modelFactory->makeUser('testBaseSecurityServiceOther_2@test.com');

        $permissionService = new BasePermissionService($otherUser, $this->entityManager);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService->CheckOrganisationId($project->getOrganisation()->getId());
    }

    public function testBasePermissionServiceWithOtherUser_ProjectId()
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $project = $this->createProjectStack('testBaseSecurityService_3@test.com');
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->getByEmail('testBaseSecurityService_3@test.com');
        $user->addOrganisation($project->getOrganisation());

        $otherUser = $modelFactory->makeUser('testBaseSecurityServiceOther_3@test.com');

        $permissionService = new BasePermissionService($otherUser, $this->entityManager);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService->CheckProjectId($project->getId());
    }
}
