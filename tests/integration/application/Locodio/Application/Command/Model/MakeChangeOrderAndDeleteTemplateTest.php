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

namespace App\Tests\integration\application\Locodio\Application\Command\Model;

use App\Locodio\Application\Command\Model\AddTemplate\AddTemplate;
use App\Locodio\Application\Command\Model\AddTemplate\AddTemplateHandler;
use App\Locodio\Application\Command\Model\ChangeTemplate\ChangeTemplate;
use App\Locodio\Application\Command\Model\ChangeTemplate\ChangeTemplateHandler;
use App\Locodio\Application\Command\Model\DeleteTemplate\DeleteTemplate;
use App\Locodio\Application\Command\Model\DeleteTemplate\DeleteTemplateHandler;
use App\Locodio\Application\Command\Model\OrderTemplate\OrderTemplate;
use App\Locodio\Application\Command\Model\OrderTemplate\OrderTemplateHandler;
use App\Locodio\Application\Query\Model\Readmodel\TemplateRM;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Model\TemplateType;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class MakeChangeOrderAndDeleteTemplateTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeTemplates(): array
    {
        $projectRepo = $this->entityManager->getRepository(Project::class);
        $templateRepo = $this->entityManager->getRepository(Template::class);
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $project = $modelFactory->makeProject(Uuid::fromString('59adee3c-126f-4e17-a561-97ff58e7af74'));
        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = $project->getId();
        $jsonCommand->type = TemplateType::DOMAIN_MODEL->value;
        $jsonCommand->name = "name";
        $jsonCommand->language = "language";
        $command = AddTemplate::hydrateFromJson($jsonCommand);
        $commandHandler = new AddTemplateHandler($projectRepo, $templateRepo);

        // -- make three templates
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();

        $templates = $templateRepo->getByProject($project);
        Assert::assertCount(3, $templates);
        return $templates;
    }

    /** @depends testMakeTemplates */
    public function testChangeTemplate(array $templates): array
    {
        $templateRepo = $this->entityManager->getRepository(Template::class);

        /** @var Template $firstTemplate */
        $firstTemplate = $templates[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstTemplate->getId();
        $jsonCommand->type = TemplateType::COMMAND->value;
        $jsonCommand->name = "changed name";
        $jsonCommand->language = "changed language";
        $jsonCommand->template = "changed template";
        $command = ChangeTemplate::hydrateFromJson($jsonCommand);
        $commandHandler = new ChangeTemplateHandler($templateRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        // -- check the changed template via the readmodel
        $template = $templateRepo->getById($firstTemplate->getId());
        $templateRM = TemplateRM::hydrateFromModel($template, true);
        $result = json_decode(json_encode($templateRM));
        Assert::assertEquals(TemplateType::COMMAND->value, $result->type);
        Assert::assertEquals('changed name', $result->name);
        Assert::assertEquals('changed language', $result->language);
        Assert::assertEquals('changed template', $result->template);

        return $templates;
    }

    /** @depends testChangeTemplate */
    public function testOrderTemplates(array $templates): array
    {
        $currentOrder = [];
        foreach ($templates as $template) {
            $currentOrder[] = $template->getId();
        }
        $newOrder = array_reverse($currentOrder);
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = $newOrder;
        $command = OrderTemplate::hydrateFromJson($jsonCommand);
        $templateRepo = $this->entityManager->getRepository(Template::class);
        $commandHandler = new OrderTemplateHandler($templateRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $projectRepo = $this->entityManager->getRepository(Project::class);
        $project = $projectRepo->getByUuid(Uuid::fromString('59adee3c-126f-4e17-a561-97ff58e7af74'));
        $templates = $templateRepo->getByProject($project);
        $resultOrder = [];
        foreach ($templates as $template) {
            $resultOrder[] = $template->getId();
        }
        Assert::assertEquals($newOrder, $resultOrder);

        return $templates;
    }

    /** @depends testOrderTemplates */
    public function testDeleteTemplate(array $templates): void
    {
        $templateRepo = $this->entityManager->getRepository(Template::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);

        /** @var Template $firstTemplate */
        $firstTemplate = $templates[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstTemplate->getId();
        $command = DeleteTemplate::hydrateFromJson($jsonCommand);
        $commandHandler = new DeleteTemplateHandler($templateRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $project = $projectRepo->getByUuid(Uuid::fromString('59adee3c-126f-4e17-a561-97ff58e7af74'));
        $templates = $templateRepo->getByProject($project);
        Assert::assertCount(2, $templates);
    }
}
