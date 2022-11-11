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

namespace App\Locodio\Application\Security;

use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\Field;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\Model\Relation;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\User\User;
use Doctrine\ORM\EntityManagerInterface;

class ModelPermissionService extends BasePermissionService
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

    // ————————————————————————————————————————————————————————————————————
    // Extended Checkers for Model
    // ————————————————————————————————————————————————————————————————————

    public function CheckDomainModelId(int $id): void
    {
        if (!$this->isolationMode) {
            $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);
            $organisation = $domainModelRepo->getById($id)->getProject()->getOrganisation();
            $this->CheckOrganisationId($organisation->getId());
        }
    }

    public function CheckDomainModelIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckDomainModelId($id);
            }
        }
    }

    public function CheckEnumId(int $id): void
    {
        if (!$this->isolationMode) {
            $enumRepo = $this->entityManager->getRepository(Enum::class);
            $organisation = $enumRepo->getById($id)->getProject()->getOrganisation();
            $this->CheckOrganisationId($organisation->getId());
        }
    }

    public function CheckEnumIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckEnumId($id);
            }
        }
    }

    public function CheckQueryId(int $id): void
    {
        if (!$this->isolationMode) {
            $queryRepo = $this->entityManager->getRepository(Query::class);
            $organisation = $queryRepo->getById($id)->getProject()->getOrganisation();
            $this->CheckOrganisationId($organisation->getId());
        }
    }

    public function CheckQueryIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckQueryId($id);
            }
        }
    }

    public function CheckCommandId(int $id): void
    {
        if (!$this->isolationMode) {
            $commandRepo = $this->entityManager->getRepository(Command::class);
            $organisation = $commandRepo->getById($id)->getProject()->getOrganisation();
            $this->CheckOrganisationId($organisation->getId());
        }
    }

    public function CheckCommandIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckCommandId($id);
            }
        }
    }

    public function CheckTemplateId(int $id): void
    {
        if (!$this->isolationMode) {
            $templateRepo = $this->entityManager->getRepository(Template::class);
            $organisation = $templateRepo->getById($id)->getProject()->getOrganisation();
            $this->CheckOrganisationId($organisation->getId());
        }
    }

    public function CheckTemplateIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckTemplateId($id);
            }
        }
    }

    public function CheckFieldId(int $id): void
    {
        if (!$this->isolationMode) {
            $fieldRepo = $this->entityManager->getRepository(Field::class);
            $organisation = $fieldRepo->getById($id)->getDomainModel()->getProject()->getOrganisation();
            $this->CheckOrganisationId($organisation->getId());
        }
    }

    public function CheckFieldIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckFieldId($id);
            }
        }
    }

    public function CheckRelationId(int $id): void
    {
        if (!$this->isolationMode) {
            $relationRepo = $this->entityManager->getRepository(Relation::class);
            $organisation = $relationRepo->getById($id)->getDomainModel()->getProject()->getOrganisation();
            $this->CheckOrganisationId($organisation->getId());
        }
    }

    public function CheckRelationIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckRelationId($id);
            }
        }
    }

    public function CheckEnumOptionId(int $id): void
    {
        if (!$this->isolationMode) {
            $enumOptionRepo = $this->entityManager->getRepository(EnumOption::class);
            $organisation = $enumOptionRepo->getById($id)->getEnum()->getProject()->getOrganisation();
            $this->CheckOrganisationId($organisation->getId());
        }
    }

    public function CheckEnumOptionIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckEnumOptionId($id);
            }
        }
    }

    public function CheckMasterTemplateId(int $id): void
    {
        if (!$this->isolationMode) {
            $masterTemplateRepo = $this->entityManager->getRepository(MasterTemplate::class);
            $masterTemplate = $masterTemplateRepo->getById($id);
            $this->CheckUserId($masterTemplate->getUser()->getId());
        }
    }

    public function CheckMasterTemplateIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckMasterTemplateId($id);
            }
        }
    }
}
