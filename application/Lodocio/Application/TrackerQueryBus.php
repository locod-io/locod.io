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

namespace App\Lodocio\Application;

use App\Locodio\Application\Query\Linear\LinearConfig;
use App\Lodocio\Application\Query\Tracker\GetTrackerNodeFileTrait;
use App\Lodocio\Application\Query\Tracker\GetTrackerNodeStatusTrait;
use App\Lodocio\Application\Query\Tracker\GetTrackerNodeTrait;
use App\Lodocio\Application\Query\Tracker\GetTrackerTrait;
use App\Lodocio\Application\Security\LodocioPermissionService;
use App\Lodocio\Domain\Model\Project\DocProjectRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeFileRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeGroupRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatusRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class TrackerQueryBus
{
    use GetTrackerTrait;
    use GetTrackerNodeStatusTrait;
    use GetTrackerNodeTrait;
    use GetTrackerNodeFileTrait;

    protected LodocioPermissionService $permission;

    public function __construct(
        protected Security                    $security,
        protected EntityManagerInterface      $entityManager,
        protected bool                        $isolationMode,
        protected LinearConfig                $linearConfig,
        protected Environment                 $twig,
        protected string                      $uploadFolder,
        protected DocProjectRepository        $docProjectRepository,
        protected TrackerRepository           $trackerRepository,
        protected TrackerNodeRepository       $trackerNodeRepository,
        protected TrackerNodeGroupRepository  $trackerNodeGroupRepository,
        protected TrackerNodeStatusRepository $trackerNodeStatusRepository,
        protected TrackerNodeFileRepository   $trackerNodeFileRepository,
    ) {
        $this->permission = new LodocioPermissionService(
            $security->getUser(),
            $entityManager,
            $this->isolationMode
        );
    }
}
