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

namespace App\Lodocio\Application\Command\Wiki;

enum ProjectDocumentType: string
{
    case WIKI = 'wiki';
    case GROUP = 'group';
    case NODE = 'node';

}
