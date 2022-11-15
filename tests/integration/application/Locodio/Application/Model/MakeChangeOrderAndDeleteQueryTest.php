<?php

namespace App\Tests\integration\application\Locodio\Application\Model;

use App\Locodio\Application\Command\Model\AddQuery\AddQuery;
use App\Locodio\Application\Command\Model\AddQuery\AddQueryHandler;
use App\Locodio\Application\Command\Model\ChangeQuery\ChangeQuery;
use App\Locodio\Application\Command\Model\ChangeQuery\ChangeQueryHandler;
use App\Locodio\Application\Command\Model\DeleteQuery\DeleteQuery;
use App\Locodio\Application\Command\Model\DeleteQuery\DeleteQueryHandler;
use App\Locodio\Application\Command\Model\OrderQuery\OrderQuery;
use App\Locodio\Application\Command\Model\OrderQuery\OrderQueryHandler;
use App\Locodio\Application\Query\Model\Readmodel\QueryRM;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class MakeChangeOrderAndDeleteQueryTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeQueries(): array
    {
        $queryRepo = $this->entityManager->getRepository(Query::class);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $domainModel = $modelFactory->makeDomainModel(Uuid::fromString('04692602-1b46-4942-8c65-e89dd75576ee'));
        $project = $domainModel->getProject();

        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = $project->getId();
        $jsonCommand->domainModelId = $domainModel->getId();
        $jsonCommand->name = "queryName";
        $command = AddQuery::hydrateFromJson($jsonCommand);
        $commandHandler = new AddQueryHandler($projectRepo, $domainModelRepo, $queryRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();

        $queries = $queryRepo->getByProject($project);
        Assert::assertCount(3, $queries);

        return $queries;
    }

    /** @depends testMakeQueries */
    public function testChangeQueries(array $queries): array
    {
        $queryRepo = $this->entityManager->getRepository(Query::class);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);

        /** @var Query $firstQuery */
        $firstQuery = $queries[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstQuery->getId();
        $jsonCommand->domainModelId = $firstQuery->getDomainModel()->getId();
        $jsonCommand->name = "changed query name";
        $jsonCommand->namespace = "namespace";
        $jsonCommand->mapping = ["mapping1", "mapping2"];
        $command = ChangeQuery::hydrateFromJson($jsonCommand);
        $commandHandler = new ChangeQueryHandler($domainModelRepo, $queryRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        // -- test the changes via the readmodel
        $query = $queryRepo->getById($firstQuery->getId());
        $queryRM = QueryRM::hydrateFromModel($query);
        $result = json_decode(json_encode($queryRM));
        Assert::assertEquals('changed query name', $result->name);
        Assert::assertEquals('namespace', $result->namespace);
        Assert::assertEquals(["mapping1", "mapping2"], $result->mapping);

        return $queries;
    }

    /** @depends testChangeQueries */
    public function testOrderQueries(array $queries): array
    {
        $currentOrder = [];
        foreach ($queries as $query) {
            $currentOrder[] = $query->getId();
        }
        $newOrder = array_reverse($currentOrder);
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = $newOrder;
        $command = OrderQuery::hydrateFromJson($jsonCommand);
        $queryRepo = $this->entityManager->getRepository(Query::class);
        $commandHandler = new OrderQueryHandler($queryRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $projectRepo = $this->entityManager->getRepository(Project::class);
        $project = $projectRepo->getByUuid(Uuid::fromString('04692602-1b46-4942-8c65-e89dd75576ee'));
        $queries = $queryRepo->getByProject($project);
        $resultOrder = [];
        foreach ($queries as $query) {
            $resultOrder[] = $query->getId();
        }
        Assert::assertEquals($newOrder, $resultOrder);

        return $queries;
    }

    /** @depends testOrderQueries */
    public function testDeleteQuery(array $queries): void
    {
        $queryRepo = $this->entityManager->getRepository(Query::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);

        /** @var Query $firstQuery */
        $firstQuery = $queries[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstQuery->getId();
        $command = DeleteQuery::hydrateFromJson($jsonCommand);
        $commandHandler = new DeleteQueryHandler($queryRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $project = $projectRepo->getByUuid(Uuid::fromString('04692602-1b46-4942-8c65-e89dd75576ee'));
        $queries = $queryRepo->getByProject($project);
        Assert::assertCount(2, $queries);
    }
}
