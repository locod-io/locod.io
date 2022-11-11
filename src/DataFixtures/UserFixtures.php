<?php

namespace App\DataFixtures;

use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\FetchType;
use App\Locodio\Domain\Model\Model\Field;
use App\Locodio\Domain\Model\Model\FieldType;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\Model\Relation;
use App\Locodio\Domain\Model\Model\RelationType;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Model\TemplateType;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class UserFixtures extends Fixture
{

    private $passwordEncoder;

    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————
    // Load fixture
    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————

    public function load(ObjectManager $manager): void
    {
        $admin = User::make(
            Uuid::v4(),
            'admin@test.com',
            'Firstname',
            'Lastname',
            ['ROLE_ADMIN'],
        );
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'admin'));
        $manager->persist($admin);

        // -- organisation and project

        $organisation = Organisation::make(Uuid::v4(), 'Organisation', 'ORG');
        $organisation->addUser($admin);
        $manager->persist($organisation);

        $project = Project::make(Uuid::v4(), 'Project', 'PRO', $organisation);
        $manager->persist($project);

        // -- template

        $template = Template::make($project, Uuid::v4(), TemplateType::DOMAIN_MODEL, 'My first template', 'PHP');
        $manager->persist($template);

        // -- domain model

        $domainModel = DomainModel::make($project, Uuid::v4(), 'MyFirstDomainModel');
        $manager->persist($domainModel);

        $field = Field::make($domainModel, Uuid::v4(), 'firstField', 191, FieldType::STRING, false, false, false, false, false);
        $manager->persist($field);

        $relation = Relation::make($domainModel, Uuid::v4(), RelationType::MTMB, 'mappedby', 'inversedby', FetchType::EXTRA_LAZY, 'orderby', 'ASC', $domainModel);
        $manager->persist($relation);

        // -- enum

        $enum = Enum::make($project, Uuid::v4(), $domainModel, 'MyFirstEnum');
        $manager->persist($enum);
        $enumOption = EnumOption::make($enum, Uuid::v4(), 'firstCode', 'firstValue');
        $manager->persist($enumOption);

        // -- command

        $command = Command::make($project, Uuid::v4(), $domainModel, 'MyFirstCommand');
        $manager->persist($command);

        // -- query

        $query = Query::make($project, Uuid::v4(), $domainModel, 'MyFirstQuery');
        $manager->persist($query);

        $manager->flush();
    }

}