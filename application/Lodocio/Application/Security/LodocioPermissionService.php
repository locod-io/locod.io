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

namespace App\Lodocio\Application\Security;

use App\Locodio\Application\Security\BasePermissionService;
use App\Locodio\Domain\Model\User\User;
use App\Lodocio\Domain\Model\Project\DocProject;
use App\Lodocio\Domain\Model\Tracker\Tracker;
use App\Lodocio\Domain\Model\Tracker\TrackerNode;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeFile;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeGroup;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatus;
use Doctrine\ORM\EntityManagerInterface;

class LodocioPermissionService extends BasePermissionService
{
    // ————————————————————————————————————————————————————————————————————
    // Constructor
    // ————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ?User                  $user,
        protected EntityManagerInterface $entityManager,
        protected bool                   $isolationMode = false
    ) {
        parent::__construct($user, $entityManager, $isolationMode);
    }

    /**
     * @throws \Exception
     */
    public function CheckDocProjectId(int $id): void
    {
        if (!$this->isolationMode) {
            $projectRepo = $this->entityManager->getRepository(DocProject::class);
            $organisation = $projectRepo->getById($id)->getProject()->getOrganisation();
            $this->CheckOrganisationId($organisation->getId());
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckTrackerId(int $id): void
    {
        if (!$this->isolationMode) {
            $trackerRepo = $this->entityManager->getRepository(Tracker::class);
            $tracker = $trackerRepo->getById($id);
            $this->CheckDocProjectId($tracker->getProject()->getId());
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckTrackersId(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckTrackerId($id);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckTrackerNodeId(int $id): void
    {
        if (!$this->isolationMode) {
            $trackerNodeRepo = $this->entityManager->getRepository(TrackerNode::class);
            $trackerNode = $trackerNodeRepo->getById($id);
            $this->CheckTrackerId($trackerNode->getTracker()->getId());
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckTrackerNodeGroupId(int $id): void
    {
        if (!$this->isolationMode) {
            $trackerNodeGroupRepo = $this->entityManager->getRepository(TrackerNodeGroup::class);
            $trackerNodeGroup = $trackerNodeGroupRepo->getById($id);
            $this->CheckTrackerId($trackerNodeGroup->getTracker()->getId());
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckTrackerNodeIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckTrackerNodeId($id);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckTrackerNodeStatusId(int $id): void
    {
        if (!$this->isolationMode) {
            $trackerNodeStatusRepo = $this->entityManager->getRepository(TrackerNodeStatus::class);
            $trackerNodeStatus = $trackerNodeStatusRepo->getById($id);
            $this->CheckTrackerId($trackerNodeStatus->getTracker()->getId());
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckTrackerNodeStatusIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckTrackerNodeStatusId($id);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckTrackerNodeFileId(int $id): void
    {
        if (!$this->isolationMode) {
            $trackerNodeFileRepo = $this->entityManager->getRepository(TrackerNodeFile::class);
            $trackerNodeFile = $trackerNodeFileRepo->getById($id);
            $this->CheckTrackerNodeId($trackerNodeFile->getTrackerNode()->getId());
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckTrackerNodeFileIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckTrackerNodeFileId($id);
            }
        }
    }

}
