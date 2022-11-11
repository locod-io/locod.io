<?php

/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\unit\application\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\User;
use Symfony\Component\Uid\Uuid;

class ModelFactory
{
    public static function makeProject(): Project
    {
        $organisation = Organisation::make(Uuid::v4(), 'organisation', 'ORG');
        return Project::make(Uuid::v4(), 'project', 'code', $organisation);
    }

    public static function makeDomainModel(): DomainModel
    {
        return DomainModel::make(self::makeProject(), Uuid::v4(), 'modelName');
    }

    public static function makeEnum(): Enum
    {
        return Enum::make(self::makeProject(), Uuid::v4(), self::makeDomainModel(), 'enumName');
    }

    public static function makeUser(): User
    {
        return User::make(Uuid::v4(), 'user@test.com', 'firstname', 'lastname', []);
    }
}
