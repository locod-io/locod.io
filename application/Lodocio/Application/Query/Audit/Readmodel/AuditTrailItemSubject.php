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

namespace App\Lodocio\Application\Query\Audit\Readmodel;

enum AuditTrailItemSubject: string
{
    case TRACKER = 'tracker';
    case TRACKER_NODE = 'trackerNode';

}
