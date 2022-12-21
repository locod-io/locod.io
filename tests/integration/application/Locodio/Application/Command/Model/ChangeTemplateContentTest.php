<?php

namespace App\Tests\integration\application\Locodio\Application\Command\Model;

use App\Locodio\Application\Command\Model\ChangeTemplateContent\ChangeTemplateContent;
use App\Locodio\Application\Command\Model\ChangeTemplateContent\ChangeTemplateContentHandler;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\Template;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class ChangeTemplateContentTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testChangeContent(): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);

        // -- make some models
        $user = $modelFactory->makeUser('changeTemplateContent@test.com');
        $masterTemplate = $modelFactory->makeMasterTemplate(
            $user,
            Uuid::fromString('a5e677ee-2ab9-4ca6-a833-86670e366286')
        );
        $template = $modelFactory->makeTemplate(Uuid::fromString('c2955afd-864a-4bb3-a3d6-2aa60ba47f29'));
        $template->importMasterTemplate($masterTemplate);

        // -- make some command and handlers
        $jsonCommand = new \stdClass();
        $jsonCommand->templateId = $template->getId();
        $jsonCommand->masterTemplateId = $masterTemplate->getId();
        $command = ChangeTemplateContent::hydrateFromJson($jsonCommand);

        $masterTemplateRepo = $this->entityManager->getRepository(MasterTemplate::class);
        $templateRepo = $this->entityManager->getRepository(Template::class);
        $commandHandler = new ChangeTemplateContentHandler($templateRepo, $masterTemplateRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $template = $templateRepo->getById($template->getId());
        Assert::assertEquals('master template', $template->getTemplate());
    }
}
