<?php

namespace App\Tests\integration\application\Locodio\Application\Query\Model;

use App\Linear\Application\Query\LinearConfig;
use App\Locodio\Application\Command\Model\ChangeDocumentor\ChangeDocumentHandler;
use App\Locodio\Application\Command\Model\ChangeDocumentor\ChangeDocumentor;
use App\Locodio\Application\Query\Model\GetDocumentor;
use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DocumentorType;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\ModelStatus;
use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Model\Query;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class GetDocumentorTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetDocumentor(): \stdClass
    {
        $dummyLinearConfig = new LinearConfig('', '', '');
        $modelFactory = new DatabaseModelFactory($this->entityManager);

        $module = $modelFactory->makeModule(Uuid::fromString('fc4494d1-e597-43e2-8e12-dc5fefe96d19'));
        $workflow = $modelFactory->makeModelStatusWorkflow($module->getProject());

        $query = new GetDocumentor(
            $this->entityManager,
            $this->entityManager->getRepository(Documentor::class),
            $this->entityManager->getRepository(Module::class),
            $this->entityManager->getRepository(DomainModel::class),
            $this->entityManager->getRepository(Enum::class),
            $this->entityManager->getRepository(Query::class),
            $this->entityManager->getRepository(Command::class),
            $this->entityManager->getRepository(ModelStatus::class),
            $dummyLinearConfig
        );
        $documentor = $query->ByTypeAndSubjectId(DocumentorType::MODULE, $module->getId());
        $this->entityManager->flush();

        $documentorResult = json_decode(json_encode($documentor));
        Assert::assertNotEquals(0, $documentorResult->id);
        Assert::assertEquals('', $documentorResult->description);
        Assert::assertEquals('start', $documentorResult->status->name);
        Assert::assertEquals(true, $documentorResult->status->isStart);

        $documentorRepo = $this->entityManager->getRepository(Documentor::class);
        $workflowDocumentor = new \stdClass();
        $workflowDocumentor->documentor = $documentorRepo->getByUuid(Uuid::fromString($documentorResult->uuid));
        $workflowDocumentor->workflow = $workflow;

        return $workflowDocumentor;
    }

    /** @depends testGetDocumentor */
    public function testChangeDocumentor(\stdClass $workflowDocumentor): void
    {
        /** @var ModelStatus $statusMiddle */
        $statusMiddle = $workflowDocumentor->workflow->statusMiddle;

        $jsonCommand = new \stdClass();
        $jsonCommand->id = $workflowDocumentor->documentor->getId();
        $jsonCommand->statusId = $statusMiddle->getId();
        $jsonCommand->description = 'changed description';
        $command = ChangeDocumentor::hydrateFromJson($jsonCommand);

        $documentorRepo = $this->entityManager->getRepository(Documentor::class);
        $modelStatusRepo = $this->entityManager->getRepository(ModelStatus::class);
        $commandHandler = new ChangeDocumentHandler($this->security, $documentorRepo, $modelStatusRepo);
        $result = $commandHandler->go($command);

        $documentorResult = $documentorRepo->getByUuid($workflowDocumentor->documentor->getUuid());
        Assert::assertEquals('changed description', $documentorResult->getDescription());
        Assert::assertEquals('middle', $documentorResult->getStatus()->getName());
    }

    // todo test extra workflow changes...
}
