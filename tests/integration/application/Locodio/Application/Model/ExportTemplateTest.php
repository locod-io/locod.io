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

namespace App\Tests\integration\application\Locodio\Application\Model;

use App\Locodio\Application\Command\Model\ExportTemplateToMasterTemplate\ExportTemplateToMasterTemplate;
use App\Locodio\Application\Command\Model\ExportTemplateToMasterTemplate\ExportTemplateToMasterTemplateHandler;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\User\User;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class ExportTemplateTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testExportTemplate(): void
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $masterTemplateRepo = $this->entityManager->getRepository(MasterTemplate::class);
        $templateRepo = $this->entityManager->getRepository(Template::class);

        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('exportTemplate@test.com');
        $template = $modelFactory->makeTemplate(Uuid::fromString('6d8f4714-1b99-42e6-860a-8a5257f40a2f'));

        $jsonCommand = new \stdClass();
        $jsonCommand->templateId = $template->getId();
        $jsonCommand->userId = $user->getId();
        $command = ExportTemplateToMasterTemplate::hydrateFromJson($jsonCommand);
        $commandHandler = new ExportTemplateToMasterTemplateHandler($userRepo, $masterTemplateRepo, $templateRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $masterTemplates = $masterTemplateRepo->getByUser($user);
        Assert::assertCount(1, $masterTemplates);
    }
}
