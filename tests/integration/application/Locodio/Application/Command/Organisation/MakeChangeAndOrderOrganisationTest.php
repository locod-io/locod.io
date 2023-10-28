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

namespace App\Tests\integration\application\Locodio\Application\Command\Organisation;

use App\Locodio\Application\Command\Organisation\AddOrganisation\AddOrganisation;
use App\Locodio\Application\Command\Organisation\AddOrganisation\AddOrganisationHandler;
use App\Locodio\Application\Command\Organisation\ChangeOrganisation\ChangeOrganisation;
use App\Locodio\Application\Command\Organisation\ChangeOrganisation\ChangeOrganisationHandler;
use App\Locodio\Application\Command\Organisation\OrderOrganisation\OrderOrganisation;
use App\Locodio\Application\Command\Organisation\OrderOrganisation\OrderOrganisationHandler;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRM;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\User\User;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;

class MakeChangeAndOrderOrganisationTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateOrganisations(): array
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('createOrganisation@test.com');

        $jsonCommand = new \stdClass();
        $jsonCommand->userId = $user->getId();
        $jsonCommand->name = 'organisation';
        $command = AddOrganisation::hydrateFromJson($jsonCommand);
        $commandHandler = new AddOrganisationHandler(
            $this->entityManager->getRepository(User::class),
            $this->entityManager->getRepository(Organisation::class)
        );
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();

        $organisationRepo = $this->entityManager->getRepository(Organisation::class);
        $organisations = $organisationRepo->getByUser($user);
        Assert::assertCount(3, $organisations);
        return $organisations;
    }

    /** @depends testCreateOrganisations */
    public function testChangeOrganisation(array $organisations): array
    {
        $organisationRepo = $this->entityManager->getRepository(Organisation::class);

        // -- change the first organisation
        /** @var Organisation $firstOrganisation */
        $firstOrganisation = $organisations[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstOrganisation->getId();
        $jsonCommand->name = 'new organisation name';
        $jsonCommand->code = 'ORG';
        $jsonCommand->color = 'color';
        $jsonCommand->linearApiKey = 'some-key';
        $command = ChangeOrganisation::hydrateFromJson($jsonCommand);
        $commandHandler = new ChangeOrganisationHandler($organisationRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        // -- test the change organisation via the readmodel
        $organisation = $organisationRepo->getById($firstOrganisation->getId());
        $organisationRM = OrganisationRM::hydrateFromModel($organisation);
        $result = json_decode(json_encode($organisationRM));
        Assert::assertEquals('new organisation name', $result->name);
        Assert::assertEquals('ORG', $result->code);
        Assert::assertEquals('#color', $result->color);
        Assert::assertEquals('some-key', $result->linearApiKey);

        return $organisations;
    }

    /** @depends testChangeOrganisation */
    public function testOrderOrganisation(array $organisations): void
    {
        $organisationRepo = $this->entityManager->getRepository(Organisation::class);
        $currenOrder = [];
        foreach ($organisations as $organisation) {
            $currenOrder[] = $organisation->getId();
        }

        // -- reverse the order
        $newOrder = array_reverse($currenOrder);
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = $newOrder;
        $command = OrderOrganisation::hydrateFromJson($jsonCommand);
        $commandHandler = new OrderOrganisationHandler($organisationRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        // -- test the new sequence
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->getByEmail('createOrganisation@test.com');
        $organisations = $organisationRepo->getByUser($user);
        Assert::assertCount(3, $organisations);
        $resultOrder = [];
        foreach ($organisations as $organisation) {
            $resultOrder[] = $organisation->getId();
        }
        Assert::assertEquals($resultOrder, $newOrder);
    }
}
