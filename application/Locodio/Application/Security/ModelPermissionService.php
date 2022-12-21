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
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DocumentorType;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\ModelStatus;
use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\Model\Association;
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

    public function CheckAttributeId(int $id): void
    {
        if (!$this->isolationMode) {
            $attributeRepo = $this->entityManager->getRepository(Attribute::class);
            $organisation = $attributeRepo->getById($id)->getDomainModel()->getProject()->getOrganisation();
            $this->CheckOrganisationId($organisation->getId());
        }
    }

    public function CheckAttributeIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckAttributeId($id);
            }
        }
    }

    public function CheckAssociationId(int $id): void
    {
        if (!$this->isolationMode) {
            $associationRepo = $this->entityManager->getRepository(Association::class);
            $organisation = $associationRepo->getById($id)->getDomainModel()->getProject()->getOrganisation();
            $this->CheckOrganisationId($organisation->getId());
        }
    }

    public function CheckAssocationIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckAssociationId($id);
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

    public function CheckModuleId(int $id): void
    {
        if (!$this->isolationMode) {
            $moduleRepo = $this->entityManager->getRepository(Module::class);
            $module = $moduleRepo->getById($id);
            $this->CheckProjectId($module->getProject()->getId());
        }
    }

    public function CheckModuleIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckModuleId($id);
            }
        }
    }

    public function CheckModelStatusId(int $id): void
    {
        if (!$this->isolationMode) {
            $modelStatusRepo = $this->entityManager->getRepository(ModelStatus::class);
            $modelStatus = $modelStatusRepo->getById($id);
            $this->CheckProjectId($modelStatus->getProject()->getId());
        }
    }

    public function CheckModelStatusIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckModelStatusId($id);
            }
        }
    }

    public function CheckDocumentorId(int $id): void
    {
        if (!$this->isolationMode) {
            $documentorRepo = $this->entityManager->getRepository(Documentor::class);
            $documentor = $documentorRepo->getById($id);
            switch ($documentor->getType()) {
                case DocumentorType::DOMAIN_MODEL:
                    $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);
                    $domainModel = $domainModelRepo->getByDocumentor($documentor);
                    $this->CheckDomainModelId($domainModel->getId());
                    break;
                case DocumentorType::ENUM:
                    $enumRepo = $this->entityManager->getRepository(Enum::class);
                    $enum = $enumRepo->getByDocumentor($documentor);
                    $this->CheckEnumId($enum->getId());
                    break;
                case DocumentorType::QUERY:
                    $queryRepo = $this->entityManager->getRepository(Query::class);
                    $query = $queryRepo->getByDocumentor($documentor);
                    $this->CheckQueryId($query->getId());
                    break;
                case DocumentorType::COMMAND:
                    $commandRepo = $this->entityManager->getRepository(Command::class);
                    $command = $commandRepo->getByDocumentor($documentor);
                    $this->CheckCommandId($command->getId());
                    break;
                default:
                    $moduleRepo = $this->entityManager->getRepository(Module::class);
                    $module = $moduleRepo->getByDocumentor($documentor);
                    $this->CheckModuleId($module->getId());
                    break;
            }
        }
    }
}
