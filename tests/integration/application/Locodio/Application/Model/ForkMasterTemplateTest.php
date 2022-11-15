<?php

namespace App\Tests\integration\application\Locodio\Application\Model;

use App\Locodio\Application\Command\Model\ForkTemplate\ForkTemplate;
use App\Locodio\Application\Command\Model\ForkTemplate\ForkTemplateHandler;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\MasterTemplateFork;
use App\Locodio\Domain\Model\Model\TemplateType;
use App\Locodio\Domain\Model\User\User;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class ForkMasterTemplateTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testForkMasterTemplate(): void
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $masterTemplateRepo = $this->entityManager->getRepository(MasterTemplate::class);
        $masterTemplateForkRepo = $this->entityManager->getRepository(MasterTemplateFork::class);

        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user1 = $modelFactory->makeUser('forkUser1@test.com');
        $user2 = $modelFactory->makeUser('forkUser2@test.com');

        // -- make a master template
        $masterTemplate = MasterTemplate::make(
            $user1,
            Uuid::fromString('3c2623fe-f1fb-4620-b4b4-db181cdea8b8'),
            TemplateType::COMMAND,
            'name',
            'language'
        );
        $masterTemplate->change(
            TemplateType::DOMAIN_MODEL,
            'fork name',
            'fork language',
            'fork template',
            true,
            'fork description',
            ['fork tag1','fork tag2']
        );
        $masterTemplateRepo->save($masterTemplate);
        $this->entityManager->flush();

        // -- fork the template
        $jsonCommand = new \stdClass();
        $jsonCommand->templateId = $masterTemplate->getId();
        $jsonCommand->userId = $user2->getId();
        $command = ForkTemplate::hydrateFromJson($jsonCommand);
        $commandHandler = new ForkTemplateHandler($masterTemplateRepo, $masterTemplateForkRepo, $userRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        // -- fetch and test the master template
        $templatesUser2 = $masterTemplateRepo->getByUser($user2);
        Assert::assertCount(1, $templatesUser2);

        /** @var MasterTemplate $templateUser2 */
        $templateUser2 = $templatesUser2[0];
        Assert::assertEquals(TemplateType::DOMAIN_MODEL, $templateUser2->getType());
        Assert::assertEquals('fork name', $templateUser2->getName());
        Assert::assertEquals('fork language', $templateUser2->getLanguage());
        Assert::assertEquals('fork template', $templateUser2->getTemplate());
        Assert::assertEquals(false, $templateUser2->isPublic());
        Assert::assertEquals('fork description', $templateUser2->getDescription());
        Assert::assertEquals(['fork tag1','fork tag2'], $templateUser2->getTags());
    }
}
