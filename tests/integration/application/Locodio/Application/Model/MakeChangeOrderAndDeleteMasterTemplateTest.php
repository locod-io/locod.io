<?php

namespace App\Tests\integration\application\Locodio\Application\Model;

use App\Locodio\Application\Command\Model\AddMasterTemplate\AddMasterTemplate;
use App\Locodio\Application\Command\Model\AddMasterTemplate\AddMasterTemplateHandler;
use App\Locodio\Application\Command\Model\ChangeMasterTemplate\ChangeMasterTemplate;
use App\Locodio\Application\Command\Model\ChangeMasterTemplate\ChangeMasterTemplateHandler;
use App\Locodio\Application\Command\Model\DeleteMasterTemplate\DeleteMasterTemplate;
use App\Locodio\Application\Command\Model\DeleteMasterTemplate\DeleteMasterTemplateHandler;
use App\Locodio\Application\Command\Model\OrderMasterTemplate\OrderMasterTemplate;
use App\Locodio\Application\Command\Model\OrderMasterTemplate\OrderMasterTemplateHandler;
use App\Locodio\Application\Query\Model\Readmodel\MasterTemplateRM;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Model\TemplateType;
use App\Locodio\Domain\Model\User\User;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;

class MakeChangeOrderAndDeleteMasterTemplateTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeMasterTemplates(): array
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('mastertemplate@test.com');

        $jsonCommand = new \stdClass();
        $jsonCommand->userId = $user->getId();
        $jsonCommand->type = TemplateType::COMMAND->value;
        $jsonCommand->name = "master template";
        $jsonCommand->language = "language";
        $command = AddMasterTemplate::hydrateFromJson($jsonCommand);

        $userRepo = $this->entityManager->getRepository(User::class);
        $masterTemplateRepo = $this->entityManager->getRepository(MasterTemplate::class);
        $commandHandler = new AddMasterTemplateHandler($userRepo, $masterTemplateRepo);

        // -- make three master templates
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();

        $masterTemplates = $masterTemplateRepo->getByUser($user);
        Assert::assertCount(3, $masterTemplates);
        return $masterTemplates;
    }

    /** @depends testMakeMasterTemplates */
    public function testChangeMasterTemplate(array $templates): array
    {
        /** @var MasterTemplate $firstMasterTemplate */
        $firstMasterTemplate = $templates[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstMasterTemplate->getId();
        $jsonCommand->type = TemplateType::DOMAIN_MODEL->value;
        $jsonCommand->name = "name";
        $jsonCommand->language = "language";
        $jsonCommand->template = "template contents";
        $jsonCommand->isPublic = true;
        $jsonCommand->description = "description";
        $jsonCommand->tags = ["tag1", "tag2"];
        $command = ChangeMasterTemplate::hydrateFromJson($jsonCommand);
        $masterTemplateRepo = $this->entityManager->getRepository(MasterTemplate::class);
        $commandHandler = new ChangeMasterTemplateHandler($masterTemplateRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        // -- check the changed template via the readmodel
        $masterTemplate = $masterTemplateRepo->getById($firstMasterTemplate->getId());
        $masterTemplateRM = MasterTemplateRM::hydrateFromModel($masterTemplate, true);
        $result = json_decode(json_encode($masterTemplateRM));
        Assert::assertEquals(TemplateType::DOMAIN_MODEL->value, $result->type);
        Assert::assertEquals('name', $result->name);
        Assert::assertEquals('language', $result->language);
        Assert::assertEquals('template contents', $result->template);
        Assert::assertEquals('description', $result->description);
        Assert::assertEquals(true, $result->isPublic);
        Assert::assertEquals(["tag1", "tag2"], $result->tags);

        return $templates;
    }

    /** @depends testChangeMasterTemplate */
    public function testOrderMasterTemplates(array $templates): array
    {
        $currentOrder = [];
        foreach ($templates as $template) {
            $currentOrder[] = $template->getId();
        }
        $newOrder = array_reverse($currentOrder);
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = $newOrder;
        $command = OrderMasterTemplate::hydrateFromJson($jsonCommand);
        $masterTemplateRepo = $this->entityManager->getRepository(MasterTemplate::class);
        $commandHandler = new OrderMasterTemplateHandler($masterTemplateRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->getByEmail('mastertemplate@test.com');
        $templates = $masterTemplateRepo->getByUser($user);

        $resultOrder = [];
        foreach ($templates as $template) {
            $resultOrder[] = $template->getId();
        }
        Assert::assertEquals($newOrder, $resultOrder);

        return $templates;
    }

    /** @depends testOrderMasterTemplates */
    public function testDeleteMasterTemplate(array $templates): void
    {
        $masterTemplateRepo = $this->entityManager->getRepository(MasterTemplate::class);
        $templateRepo = $this->entityManager->getRepository(Template::class);
        $userRepo = $this->entityManager->getRepository(User::class);

        /** @var MasterTemplate $firstTemplate */
        $firstTemplate = $templates[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstTemplate->getId();
        $command = DeleteMasterTemplate::hydrateFromJson($jsonCommand);
        $commandHandler = new DeleteMasterTemplateHandler($templateRepo, $masterTemplateRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $user = $userRepo->getByEmail('mastertemplate@test.com');
        $templates = $masterTemplateRepo->getByUser($user);
        Assert::assertCount(2, $templates);
    }
}
