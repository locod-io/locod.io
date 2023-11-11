<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Lodocio\Application\Command\Tracker\ChangeTrackerFileName;

use App\Lodocio\Infrastructure\Database\Tracker\TrackerNodeFileRepository;

class ChangeTrackerFileNameHandler
{
    public function __construct(
        protected TrackerNodeFileRepository $fileRepository,
    ) {
    }

}
