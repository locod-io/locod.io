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

namespace App\Lodocio\Application\Query\Audit;

use App\Lodocio\Application\Query\Audit\Readmodel\AuditTrailCollection;
use App\Lodocio\Application\Query\Audit\Readmodel\AuditTrailItem;
use App\Lodocio\Application\Query\Audit\Readmodel\AuditTrailItemSubject;
use App\Locodio\Application\Query\User\Readmodel\UserRM;
use App\Locodio\Domain\Model\User\InterfaceTheme;
use App\Locodio\Domain\Model\User\UserRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNode;
use DH\Auditor\Model\Entry;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use DH\Auditor\Provider\ProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class GetAuditTrail
{
    protected Reader $reader;
    protected array $userCache;
    protected int $pageSize = 1000;

    public function __construct(
        protected ProviderInterface      $provider,
        protected UserRepository         $userRepository,
        protected EntityManagerInterface $entityManager,
    ) {
        $this->reader = new Reader($this->provider);
        $this->userCache = [];
    }

    public function getNodeActivityById(int $id): AuditTrailCollection
    {
        $collection = new AuditTrailCollection();

        $trackerNodeRepo = $this->entityManager->getRepository(TrackerNode::class);
        $node = $trackerNodeRepo->getById($id);

        // -- make the audit collection for the module
        $query = $this->reader->createQuery(TrackerNode::class, ['object_id' => $id, 'page_size' => $this->pageSize]);
        $audits = $query->execute();
        foreach ($audits as $audit) {
            $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::TRACKER_NODE));
        }

        // -- sort the audit collection
        $collection->sortByDateDesc();

        return $collection;
    }



    // -----------------------------------------------------------------------------------------------------------------
    // private functions
    // -----------------------------------------------------------------------------------------------------------------

    private function compileItem(Entry $audit, AuditTrailItemSubject $subject): AuditTrailItem
    {
        $item = AuditTrailItem::hydrateFromModel($audit);
        $item->setSubject($subject);
        if (!is_null($audit->getUserId())) {
            $user = $this->getUserDetail((int)$audit->getUserId());
            $item->setInitialsAndColor($user->getInitials(), $user->getColor());
        } else {
            $item->setInitialsAndColor('BOT', '#CCC');
        }
        return $item;
    }

    private function getUserDetail(int $id): UserRM
    {
        if (!isset($this->userCache[$id])) {
            $user = $this->userRepository->findById($id);
            if (is_null($user)) {
                $userRM = new UserRM(
                    $id,
                    Uuid::v4()->toRfc4122(),
                    'X',
                    'X',
                    '',
                    '#CCC',
                    InterfaceTheme::LIGHT->value,
                    'Workspace'
                );
            } else {
                $userRM = UserRM::hydrateFromModel($user, false);
            }
            $this->userCache[$id] = $userRM;
        }
        return $this->userCache[$id];
    }

}
