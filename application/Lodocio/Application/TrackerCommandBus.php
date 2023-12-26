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

use App\Figma\FigmaConfig;
use App\Linear\Application\Query\LinearConfig;
use App\Lodocio\Application\Command\Tracker\AddProjectDocument\AddProjectDocumentTrait;
use App\Lodocio\Application\Command\Tracker\AddTracker\AddTrackerTrait;
use App\Lodocio\Application\Command\Tracker\AddTrackerNodeStatus\AddTrackerNodeStatusTrait;
use App\Lodocio\Application\Command\Tracker\ChangeGroupName\ChangeGroupNameTrait;
use App\Lodocio\Application\Command\Tracker\ChangeNodeDescription\ChangeNodeDescriptionTrait;
use App\Lodocio\Application\Command\Tracker\ChangeNodeName\ChangeNodeNameTrait;
use App\Lodocio\Application\Command\Tracker\ChangeNodeRelatedIssues\ChangeNodeRelatedIssuesTrait;
use App\Lodocio\Application\Command\Tracker\ChangeProjectDocument\ChangeProjectDocumentTrait;
use App\Lodocio\Application\Command\Tracker\ChangeStatusNode\ChangeStatusNodeTrait;
use App\Lodocio\Application\Command\Tracker\ChangeTracker\ChangeTrackerTrait;
use App\Lodocio\Application\Command\Tracker\ChangeTrackerFileName\ChangeTrackerFileNameTrait;
use App\Lodocio\Application\Command\Tracker\ChangeTrackerNodeStatus\ChangeTrackerNodeStatusTrait;
use App\Lodocio\Application\Command\Tracker\DeleteProjectDocument\DeleteProjectDocumentTrait;
use App\Lodocio\Application\Command\Tracker\DeleteTracker\DeleteTrackerTrait;
use App\Lodocio\Application\Command\Tracker\DeleteTrackerFile\DeleteTrackerFileTrait;
use App\Lodocio\Application\Command\Tracker\DeleteTrackerNode\DeleteTrackerNodeTrait;
use App\Lodocio\Application\Command\Tracker\DeleteTrackerNodeGroup\DeleteTrackerNodeGroupTrait;
use App\Lodocio\Application\Command\Tracker\DeleteTrackerNodeStatus\DeleteTrackerNodeStatusTrait;
use App\Lodocio\Application\Command\Tracker\OrderTracker\OrderTrackerTrait;
use App\Lodocio\Application\Command\Tracker\OrderTrackerFile\OrderTrackerFileTrait;
use App\Lodocio\Application\Command\Tracker\OrderTrackerNodeStatus\OrderTrackerNodeStatusTrait;
use App\Lodocio\Application\Command\Tracker\SaveTrackerNodeStatusWorkflow\SaveTrackerNodeStatusWorkflowTrait;
use App\Lodocio\Application\Command\Tracker\SyncTrackerStructure\SyncTrackerStructureTrait;
use App\Lodocio\Application\Command\Tracker\UploadFigmaExportImage\UploadFigmaExportImageTrait;
use App\Lodocio\Application\Command\Tracker\UploadTrackerFile\UploadTrackerFileTrait;
use App\Lodocio\Application\Security\LodocioPermissionService;
use App\Lodocio\Domain\Model\Project\DocProjectRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeFileRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeGroupRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRelatedProjectDocumentRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRepository;
use App\Lodocio\Infrastructure\Database\Tracker\TrackerNodeRepository;
use App\Lodocio\Infrastructure\Database\Tracker\TrackerNodeStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class TrackerCommandBus
{
    use AddTrackerTrait;
    use ChangeTrackerTrait;
    use OrderTrackerTrait;
    use DeleteTrackerTrait;
    use AddTrackerNodeStatusTrait;
    use ChangeTrackerNodeStatusTrait;
    use OrderTrackerNodeStatusTrait;
    use SaveTrackerNodeStatusWorkflowTrait;
    use SyncTrackerStructureTrait;
    use ChangeGroupNameTrait;
    use ChangeNodeNameTrait;
    use ChangeNodeDescriptionTrait;
    use ChangeNodeRelatedIssuesTrait;
    use ChangeStatusNodeTrait;
    use DeleteTrackerNodeStatusTrait;
    use DeleteTrackerNodeTrait;
    use DeleteTrackerNodeGroupTrait;
    use UploadTrackerFileTrait;
    use ChangeTrackerFileNameTrait;
    use DeleteTrackerFileTrait;
    use OrderTrackerFileTrait;
    use AddProjectDocumentTrait;
    use DeleteProjectDocumentTrait;
    use UploadFigmaExportImageTrait;

    protected LodocioPermissionService $permission;

    public function __construct(
        protected Security                                $security,
        protected EntityManagerInterface                  $entityManager,
        protected bool                                    $isolationMode,
        protected string                                  $uploadFolder,
        protected LinearConfig                            $linearConfig,
        protected FigmaConfig                             $figmaConfig,
        protected DocProjectRepository                    $docProjectRepository,
        protected TrackerRepository                       $trackerRepository,
        protected TrackerNodeRepository                   $trackerNodeRepository,
        protected TrackerNodeGroupRepository              $trackerNodeGroupRepository,
        protected TrackerNodeStatusRepository             $trackerNodeStatusRepository,
        protected TrackerNodeFileRepository               $trackerNodeFileRepository,
        protected TrackerRelatedProjectDocumentRepository $documentRepository,
    ) {
        $this->permission = new LodocioPermissionService(
            $security->getUser(),
            $entityManager,
            $this->isolationMode
        );
    }
}
