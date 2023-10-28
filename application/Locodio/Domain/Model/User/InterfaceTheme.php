<?php

declare(strict_types=1);

namespace App\Locodio\Domain\Model\User;

enum InterfaceTheme: string
{
    case LIGHT = 'light';
    case DARK = 'dark';

}
