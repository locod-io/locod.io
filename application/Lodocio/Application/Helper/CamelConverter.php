<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Application\Helper;

class CamelConverter
{
    public static function CamelToKebab($input)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $input));
    }

    public static function CamelToSnake($input)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    public static function KebabToCamel($input)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $input))));
    }

    public static function SnakeToCamel($input)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $input))));
    }
}
