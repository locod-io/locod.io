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

namespace App\Tests\unit\application\Locodio\Domain\Model;

use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DocumentorType;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\ModelStatus;
use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Model\TemplateType;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\User;
use Symfony\Component\Uid\Uuid;

class ModelFactory
{
    public static function makeMasterTemplate(): MasterTemplate
    {
        $masterTemplate = MasterTemplate::make(
            self::makeUser(),
            Uuid::v4(),
            TemplateType::COMMAND,
            'template',
            'language'
        );
        $masterTemplate->identify(1, Uuid::v4());
        $masterTemplate->setSequence(10);
        return $masterTemplate;
    }

    public static function makeOrganisation(): Organisation
    {
        $organisation = Organisation::make(Uuid::v4(), 'organisation', 'ORG');
        $organisation->change('organisation', 'ORG', '#CCC', 'some-linear-key','some-figma-key', 'some-slug');
        $organisation->identify(1, Uuid::v4());
        $organisation->setSequence(10);
        return $organisation;
    }

    public static function makeProject(): Project
    {
        $project = Project::make(Uuid::v4(), 'project', 'PRO', self::makeOrganisation());
        $project->identify(1, Uuid::v4());
        $project->setSequence(10);
        return $project;
    }

    public static function makeDomainModel(): DomainModel
    {
        $domainModel = DomainModel::make(self::makeProject(), Uuid::v4(), 'modelName');
        $domainModel->identify(1, Uuid::v4());
        $domainModel->setSequence(10);
        return $domainModel;
    }

    public static function makeEnum(): Enum
    {
        $enum = Enum::make(self::makeProject(), Uuid::v4(), self::makeDomainModel(), 'enumName');
        $enum->identify(1, Uuid::v4());
        $enum->setSequence(10);
        return $enum;
    }

    public static function makeUser(): User
    {
        $user = User::make(Uuid::v4(), 'user@test.com', 'firstname', 'lastname', []);
        $user->identify(1, Uuid::v4());
        return $user;
    }

    public static function makeModule(): Module
    {
        $module = Module::make(self::makeProject(), Uuid::v4(), 'module', 'module namespace');
        $module->identify(1, Uuid::v4());
        return $module;
    }

    public static function makeModelStatus(): ModelStatus
    {
        $status = ModelStatus::make(
            self::makeProject(),
            Uuid::v4(),
            'status',
            'color',
            false,
            false,
        );
        $status->identify(1, Uuid::v4());
        return $status;
    }

    public static function makeDocumentor(DocumentorType $type): Documentor
    {
        $documentor = Documentor::make(
            Uuid::v4(),
            $type,
            'documentor',
            self::makeModelStatus()
        );
        $documentor->identify(1, Uuid::v4());
        return $documentor;
    }
}
