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

namespace App\Lodocio\Application\Query\Tracker;

use App\Locodio\Application\Query\Linear\Readmodel\IssueCacheReadModelCollection;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerNodeFileReadModel;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerReadModel;

trait GetTrackerNodeFileTrait
{
    /**
     * @throws \Exception
     */
    public function getTrackerFileById(int $id): TrackerNodeFileReadModel
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerNodeFileId($id);
        $query = new GetTrackerFile(
            $this->trackerNodeFileRepository,
        );
        return $query->ById($id);
    }

}
