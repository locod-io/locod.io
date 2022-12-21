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

namespace App\Locodio\Domain\Model\Model;

enum DocumentorType: string
{
    case MODULE = 'module';
    case DOMAIN_MODEL = 'domain-model';
    case ATTRIBUTE = 'attribute';
    case ASSOCIATION = 'association';
    case ENUM = 'enum';
    case ENUM_OPTION = 'enumOption';
    case QUERY = 'query';
    case COMMAND = 'command';
}
