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

namespace App\Tests\integration;

use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\ModelStatus;
use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Model\TemplateType;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class DatabaseModelFactory
{
    public function __construct(protected EntityManagerInterface $em)
    {
    }

    public function makeUser(string $email): User
    {
        $userRepo = $this->em->getRepository(User::class);
        $user = User::make($userRepo->nextIdentity(), $email, 'Firstname', 'Lastname', []);
        $user->setPassword(Uuid::v4()->toRfc4122());
        $userRepo->save($user);
        $this->em->flush();
        return $user;
    }

    public function makeOrganisation(Uuid $uuid): Organisation
    {
        $organisationRepo = $this->em->getRepository(Organisation::class);
        $organisation = Organisation::make($uuid, 'organisation', 'ORG');
        $organisationRepo->save($organisation);
        $this->em->flush();
        return $organisation;
    }

    public function makeProject(Uuid $uuid): Project
    {
        $projectRepo = $this->em->getRepository(Project::class);
        $project = Project::make(
            $uuid,
            'project',
            'PRO',
            $this->makeOrganisation($uuid)
        );
        $projectRepo->save($project);
        $this->em->flush();
        return $project;
    }

    public function makeModule(Uuid $uuid): Module
    {
        $moduleRepo = $this->em->getRepository(Module::class);
        $project = $this->makeProject($uuid);
        $module = Module::make($project, $uuid, 'module', 'module namespace');
        $moduleRepo->save($module);
        $this->em->flush();
        return $module;
    }

    public function makeDomainModel(Uuid $uuid): DomainModel
    {
        $domainModelRepo = $this->em->getRepository(DomainModel::class);
        $domainModel = DomainModel::make($this->makeProject($uuid), $uuid, 'name');
        $domainModel->change('name', 'namespace', 'repository');
        $domainModelRepo->save($domainModel);
        $this->em->flush();
        return $domainModel;
    }

    public function makeEnum(Uuid $uuid): Enum
    {
        $enumRepo = $this->em->getRepository(Enum::class);
        $domainModel = $this->makeDomainModel($uuid);
        $enum = Enum::make($this->makeProject($uuid), $uuid, $domainModel, 'name');
        $enum->change($domainModel, 'name', 'namespace');
        $enumRepo->save($enum);
        $this->em->flush();
        return $enum;
    }

    public function makeTemplate(Uuid $uuid): Template
    {
        $templateRepo = $this->em->getRepository(Template::class);
        $template = Template::make(
            $this->makeProject($uuid),
            $uuid,
            TemplateType::ENUM,
            'name',
            'language'
        );
        $template->change(
            TemplateType::DOMAIN_MODEL,
            'name',
            'language',
            'template'
        );
        $templateRepo->save($template);
        $this->em->flush();
        return $template;
    }

    public function makeMasterTemplate(User $user, Uuid $uuid): MasterTemplate
    {
        $masterTemplateRepo = $this->em->getRepository(MasterTemplate::class);
        $masterTemplate = MasterTemplate::make(
            $user,
            $uuid,
            TemplateType::COMMAND,
            'name',
            'language'
        );
        $masterTemplate->change(
            TemplateType::DOMAIN_MODEL,
            'name',
            'language',
            'master template',
            true,
            'description',
            ['tag1', 'tag2']
        );
        $masterTemplateRepo->save($masterTemplate);
        $this->em->flush();
        return $masterTemplate;
    }

    public function makeModelStatus(Uuid $uuid): ModelStatus
    {
        $modelStatusRepo = $this->em->getRepository(ModelStatus::class);
        $status = ModelStatus::make(
            $this->makeProject($uuid),
            $uuid,
            'status',
            'color',
            false,
            false
        );
        $modelStatusRepo->save($status);
        $this->em->flush();
        return $status;
    }

    /** return ModelStatus[] */
    public function makeModelStatusWorkflow(Project $project): \stdClass
    {
        $modelStatusRepo = $this->em->getRepository(ModelStatus::class);
        $statusStart = ModelStatus::make($project, Uuid::v4(), 'start', 'blue', true, false);
        $statusMiddle = ModelStatus::make($project, Uuid::v4(), 'middle', 'yellow', false, false);
        $statusFinal = ModelStatus::make($project, Uuid::v4(), 'final', 'green', false, true);
        $modelStatusRepo->save($statusStart);
        $modelStatusRepo->save($statusMiddle);
        $modelStatusRepo->save($statusFinal);
        $this->em->flush();

        $defaultPosition = new \stdClass();
        $defaultPosition->x = 0;
        $defaultPosition->y = 0;

        // -- set the workflow
        $statusStart->setWorkflow($defaultPosition, [], [$statusMiddle->getId(), $statusFinal->getId()]);
        $statusMiddle->setWorkflow($defaultPosition, [$statusStart->getId(), $statusFinal->getId()], [$statusFinal->getId()]);
        $statusFinal->setWorkflow($defaultPosition, [$statusStart->getId(), $statusMiddle->getId()], [$statusMiddle->getId()]);
        $modelStatusRepo->save($statusStart);
        $modelStatusRepo->save($statusMiddle);
        $modelStatusRepo->save($statusFinal);
        $this->em->flush();

        $workflow = new \stdClass();
        $workflow->statusStart = $statusStart;
        $workflow->statusMiddle = $statusMiddle;
        $workflow->statusFinal = $statusFinal;
        return $workflow;
    }
}
