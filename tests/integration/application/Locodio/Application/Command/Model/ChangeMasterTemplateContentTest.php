<?php

namespace App\Tests\integration\application\Locodio\Application\Command\Model;

use App\Locodio\Application\Command\Model\ChangeMasterTemplateContent\ChangeMasterTemplateContent;
use App\Locodio\Application\Command\Model\ChangeMasterTemplateContent\ChangeMasterTemplateContentHandler;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\Template;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class ChangeMasterTemplateContentTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testChangeContent(): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);

        // -- make some models
        $user = $modelFactory->makeUser('changeMasterTemplateContent@test.com');
        $masterTemplate = $modelFactory->makeMasterTemplate(
            $user,
            Uuid::fromString('6d7c264e-4bd4-420f-bc5a-9b19e6f54775')
        );
        $template = $modelFactory->makeTemplate(Uuid::fromString('f9406eb5-e64e-4319-ac06-2ccb1fee15eb'));
        $template->importMasterTemplate($masterTemplate);

        // -- make some command and handlers
        $jsonCommand = new \stdClass();
        $jsonCommand->templateId = $template->getId();
        $jsonCommand->masterTemplateId = $masterTemplate->getId();
        $command = ChangeMasterTemplateContent::hydrateFromJson($jsonCommand);

        $masterTemplateRepo = $this->entityManager->getRepository(MasterTemplate::class);
        $templateRepo = $this->entityManager->getRepository(Template::class);
        $commandHandler = new ChangeMasterTemplateContentHandler($templateRepo, $masterTemplateRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $masterTemplate = $masterTemplateRepo->getById($masterTemplate->getId());
        Assert::assertEquals('template', $masterTemplate->getTemplate());
    }
}
