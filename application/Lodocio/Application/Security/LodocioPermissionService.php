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
use App\Lodocio\Domain\Model\Wiki\Wiki;
use App\Lodocio\Domain\Model\Wiki\WikiNode;
use App\Lodocio\Domain\Model\Wiki\WikiNodeFile;
use App\Lodocio\Domain\Model\Wiki\WikiNodeGroup;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatus;
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

    // ————————————————————————————————————————————————————————————————————
    // -- tracker security checkers
    // ————————————————————————————————————————————————————————————————————

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

    // ————————————————————————————————————————————————————————————————————
    // -- wiki security checkers
    // ————————————————————————————————————————————————————————————————————
    /**
     * @throws \Exception
     */
    public function CheckWikiId(int $id): void
    {
        if (!$this->isolationMode) {
            $wikiRepo = $this->entityManager->getRepository(Wiki::class);
            $wiki = $wikiRepo->getById($id);
            $this->CheckDocProjectId($wiki->getProject()->getId());
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckWikisId(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckWikiId($id);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckWikiNodeId(int $id): void
    {
        if (!$this->isolationMode) {
            $wikiNodeRepo = $this->entityManager->getRepository(WikiNode::class);
            $wikiNode = $wikiNodeRepo->getById($id);
            $this->CheckWikiId($wikiNode->getWiki()->getId());
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckWikiNodeGroupId(int $id): void
    {
        if (!$this->isolationMode) {
            $wikiNodeGroupRepo = $this->entityManager->getRepository(WikiNodeGroup::class);
            $wikiNodeGroup = $wikiNodeGroupRepo->getById($id);
            $this->CheckWikiId($wikiNodeGroup->getWiki()->getId());
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckWikiNodeIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckWikiNodeId($id);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckWikiNodeStatusId(int $id): void
    {
        if (!$this->isolationMode) {
            $wikiNodeStatusRepo = $this->entityManager->getRepository(WikiNodeStatus::class);
            $wikiNodeStatus = $wikiNodeStatusRepo->getById($id);
            $this->CheckWikiId($wikiNodeStatus->getWiki()->getId());
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckWikiNodeStatusIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckWikiNodeStatusId($id);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckWikiNodeFileId(int $id): void
    {
        if (!$this->isolationMode) {
            $wikiNodeFileRepo = $this->entityManager->getRepository(WikiNodeFile::class);
            $wikiNodeFile = $wikiNodeFileRepo->getById($id);
            $this->CheckWikiNodeId($wikiNodeFile->getWikiNode()->getId());
        }
    }

    /**
     * @throws \Exception
     */
    public function CheckWikiNodeFileIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckWikiNodeFileId($id);
            }
        }
    }

}
