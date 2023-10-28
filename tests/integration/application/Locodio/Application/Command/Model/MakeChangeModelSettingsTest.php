<?php

namespace App\Tests\integration\application\Locodio\Application\Command\Model;

use App\Locodio\Application\Command\Model\ChangeModelSettings\ChangeModelSettings;
use App\Locodio\Application\Command\Model\ChangeModelSettings\ChangeModelSettingsHandler;
use App\Locodio\Domain\Model\Model\ModelSettings;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class MakeChangeModelSettingsTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeModelSettings(): ModelSettings
    {
        $modelSettingsRepo = $this->entityManager->getRepository(ModelSettings::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);

        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $project = $modelFactory->makeProject(Uuid::fromString('fbbc3815-ad8a-4a3d-8325-64783a92310e'));

        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = $project->getId();
        $jsonCommand->id = 0;
        $jsonCommand->domainLayer = 'domainLayer';
        $jsonCommand->applicationLayer = 'applicationLayer';
        $jsonCommand->infrastructureLayer = 'infrastructureLayer';
        $jsonCommand->teams = ['team1','team2'];
        $command = ChangeModelSettings::hydrateFromJson($jsonCommand);

        $commandHandler = new ChangeModelSettingsHandler($projectRepo, $modelSettingsRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $project = $projectRepo->getByUuid(Uuid::fromString('fbbc3815-ad8a-4a3d-8325-64783a92310e'));
        $modelSettings = $project->getModelSettings();
        Assert::assertEquals('domainLayer', $modelSettings->getDomainLayer());
        Assert::assertEquals('applicationLayer', $modelSettings->getApplicationLayer());
        Assert::assertEquals('infrastructureLayer', $modelSettings->getInfrastructureLayer());
        Assert::assertEquals(['team1','team2'], $modelSettings->getLinearTeams());

        return $modelSettings;
    }

    /** @depends testMakeModelSettings */
    public function testChangeModelSettings(ModelSettings $modelSettings): void
    {
        $modelSettingsRepo = $this->entityManager->getRepository(ModelSettings::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);

        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = $modelSettings->getProject()->getId();
        $jsonCommand->id = $modelSettings->getId();
        $jsonCommand->domainLayer = 'domainLayerChanged';
        $jsonCommand->applicationLayer = 'applicationLayerChanged';
        $jsonCommand->infrastructureLayer = 'infrastructureLayerChanged';
        $jsonCommand->teams = ['team1','team3'];
        $command = ChangeModelSettings::hydrateFromJson($jsonCommand);

        $commandHandler = new ChangeModelSettingsHandler($projectRepo, $modelSettingsRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $project = $projectRepo->getByUuid(Uuid::fromString('fbbc3815-ad8a-4a3d-8325-64783a92310e'));
        $modelSettingsSaved = $project->getModelSettings();
        Assert::assertEquals($modelSettings->getId(), $modelSettingsSaved->getId());
        Assert::assertEquals('domainLayerChanged', $modelSettingsSaved->getDomainLayer());
        Assert::assertEquals('applicationLayerChanged', $modelSettingsSaved->getApplicationLayer());
        Assert::assertEquals('infrastructureLayerChanged', $modelSettingsSaved->getInfrastructureLayer());
        Assert::assertEquals(['team1','team3'], $modelSettingsSaved->getLinearTeams());
    }
}
