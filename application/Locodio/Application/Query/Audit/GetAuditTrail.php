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

namespace App\Locodio\Application\Query\Audit;

use App\Locodio\Application\Query\User\Readmodel\UserRM;
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\User\InterfaceTheme;
use App\Locodio\Domain\Model\User\UserRepository;
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

    public function getAuditTrailForDomainModel(int $id): AuditTrailCollection
    {
        $collection = new AuditTrailCollection();

        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);
        $domainModel = $domainModelRepo->getById($id);

        // -- make the audit collection for the domain model
        $query = $this->reader->createQuery(DomainModel::class, ['object_id' => $id, 'page_size' => $this->pageSize]);
        $audits = $query->execute();
        foreach ($audits as $audit) {
            $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::DOMAIN_MODEL));
        }

        // -- make the audit collection for the attributes
        foreach ($domainModel->getAttributes() as $attribute) {
            $query = $this->reader->createQuery(Attribute::class, ['object_id' => $attribute->getId(), 'page_size' => $this->pageSize]);
            $audits = $query->execute();
            foreach ($audits as $audit) {
                $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::ATTRIBUTE));
            }
        }

        // -- make the audit collection for the associations
        foreach ($domainModel->getAssociations() as $association) {
            $query = $this->reader->createQuery(Attribute::class, ['object_id' => $association->getId(), 'page_size' => $this->pageSize]);
            $audits = $query->execute();
            foreach ($audits as $audit) {
                $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::ASSOCIATION));
            }
        }

        // -- make the audit collection for the documentor
        $query = $this->reader->createQuery(Documentor::class, ['object_id' => $domainModel->getDocumentor()->getId(), 'page_size' => $this->pageSize]);
        $audits = $query->execute();
        foreach ($audits as $audit) {
            $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::DOCUMENTOR));
        }

        // -- sort the audit collection
        $collection->sortByDateDesc();

        return $collection;
    }

    public function getAuditTrailForModule(int $id): AuditTrailCollection
    {
        $collection = new AuditTrailCollection();

        $moduleRepo = $this->entityManager->getRepository(Module::class);
        $module = $moduleRepo->getById($id);

        // -- make the audit collection for the module
        $query = $this->reader->createQuery(Module::class, ['object_id' => $id, 'page_size' => $this->pageSize]);
        $audits = $query->execute();
        foreach ($audits as $audit) {
            $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::MODULE));
        }

        // -- make the audit collection for the documentor
        $query = $this->reader->createQuery(Documentor::class, ['object_id' => $module->getDocumentor()->getId(), 'page_size' => $this->pageSize]);
        $audits = $query->execute();
        foreach ($audits as $audit) {
            $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::DOCUMENTOR));
        }

        // -- sort the audit collection
        $collection->sortByDateDesc();

        return $collection;
    }

    public function getAuditTrailForEnum(int $id): AuditTrailCollection
    {
        $collection = new AuditTrailCollection();

        $enumRepo = $this->entityManager->getRepository(Enum::class);
        $enum = $enumRepo->getById($id);

        // -- make the audit collection for the enum
        $query = $this->reader->createQuery(Enum::class, ['object_id' => $id, 'page_size' => $this->pageSize]);
        $audits = $query->execute();
        foreach ($audits as $audit) {
            $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::ENUM));
        }

        // -- make the audit collection for the enum options
        foreach ($enum->getOptions() as $option) {
            $query = $this->reader->createQuery(EnumOption::class, ['object_id' => $option->getId(), 'page_size' => $this->pageSize]);
            $audits = $query->execute();
            foreach ($audits as $audit) {
                $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::ATTRIBUTE));
            }
        }

        // -- make the audit collection for the documentor
        $query = $this->reader->createQuery(Documentor::class, ['object_id' => $enum->getDocumentor()->getId(), 'page_size' => $this->pageSize]);
        $audits = $query->execute();
        foreach ($audits as $audit) {
            $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::DOCUMENTOR));
        }

        // -- sort the audit collection
        $collection->sortByDateDesc();

        return $collection;
    }

    public function getAuditTrailForCommand(int $id): AuditTrailCollection
    {
        $collection = new AuditTrailCollection();

        $commandRepo = $this->entityManager->getRepository(Command::class);
        $command = $commandRepo->getById($id);

        // -- make the audit collection for the command
        $query = $this->reader->createQuery(Command::class, ['object_id' => $id, 'page_size' => $this->pageSize]);
        $audits = $query->execute();
        foreach ($audits as $audit) {
            $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::COMMAND));
        }

        // -- make the audit collection for the documentor
        $query = $this->reader->createQuery(Documentor::class, ['object_id' => $command->getDocumentor()->getId(), 'page_size' => $this->pageSize]);
        $audits = $query->execute();
        foreach ($audits as $audit) {
            $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::DOCUMENTOR));
        }

        // -- sort the audit collection
        $collection->sortByDateDesc();

        return $collection;
    }

    public function getAuditTrailForQuery(int $id): AuditTrailCollection
    {
        $collection = new AuditTrailCollection();

        $queryRepo = $this->entityManager->getRepository(Query::class);
        $queryModel = $queryRepo->getById($id);

        // -- make the audit collection for the command
        $query = $this->reader->createQuery(Query::class, ['object_id' => $id, 'page_size' => $this->pageSize]);
        $audits = $query->execute();
        foreach ($audits as $audit) {
            $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::QUERY));
        }

        // -- make the audit collection for the documentor
        $query = $this->reader->createQuery(Documentor::class, ['object_id' => $queryModel->getDocumentor()->getId(), 'page_size' => $this->pageSize]);
        $audits = $query->execute();
        foreach ($audits as $audit) {
            $collection->addItem($this->compileItem($audit, AuditTrailItemSubject::DOCUMENTOR));
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
                    '',
                    'Workspace',
                );
            } else {
                $userRM = UserRM::hydrateFromModel($user, false);
            }
            $this->userCache[$id] = $userRM;
        }
        return $this->userCache[$id];
    }

}
