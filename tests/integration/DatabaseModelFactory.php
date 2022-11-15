<?php

namespace App\Tests\integration;

use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\MasterTemplate;
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
        $user->setPassword(Uuid::v4());
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
            'template',
            true,
            'description',
            ['tag1', 'tag2']
        );
        $masterTemplateRepo->save($masterTemplate);
        $this->em->flush();
        return $masterTemplate;
    }
}
