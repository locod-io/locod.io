<?php

declare(strict_types=1);

namespace App\Locodio\Domain\Model\User;

enum UserRole: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';
    case ROLE_VIEWER = 'ROLE_VIEWER';
    case ROLE_ORGANISATION_VIEWER = 'ROLE_ORGANISATION_VIEWER';
    case ROLE_ORGANISATION_ADMIN = 'ROLE_ORGANISATION_ADMIN';
    case ROLE_ORGANISATION_USER = 'ROLE_ORGANISATION_USER';

}
