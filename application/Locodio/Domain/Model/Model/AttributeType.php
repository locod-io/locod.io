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

enum AttributeType: string
{
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case STRING = 'string';
    case TEXT = 'text';
    case BOOLEAN = 'boolean';
    case DATE = 'date';
    case DATE_IMMUTABLE = 'date_immutable';
    case DATE_TIME = 'date_time';
    case DATE_TIME_IMMUTABLE = 'date_time_immutable';
    case ARRAY = 'array';
    case SIMPLE_ARRAY = 'simple_array';
    case JSON = 'json';
    case UUID = 'uuid';
    case EMAIL = 'email';
    case ENUM = 'enum';
}
