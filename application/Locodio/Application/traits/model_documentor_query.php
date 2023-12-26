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

namespace App\Locodio\Application\traits;

use App\Linear\Application\Query\Readmodel\IssueReadModelCollection;
use App\Locodio\Application\Query\Model\GetDocumentor;
use App\Locodio\Application\Query\Model\Readmodel\DocumentorRM;
use App\Locodio\Domain\Model\Model\DocumentorType;
use App\Locodio\Domain\Model\User\UserRole;

trait model_documentor_query
{
    public function getDocumentorById(int $id): DocumentorRM
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckDocumentorId($id);

        $query = new GetDocumentor(
            $this->entityManager,
            $this->documentorRepository,
            $this->moduleRepository,
            $this->domainModelRepo,
            $this->enumRepo,
            $this->queryRepo,
            $this->commandRepo,
            $this->modelStatusRepo,
            $this->linearConfig
        );

        return $query->ById($id);
    }

    public function getDocumentorRelatedIssues(int $id): IssueReadModelCollection
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckDocumentorId($id);

        $query = new GetDocumentor(
            $this->entityManager,
            $this->documentorRepository,
            $this->moduleRepository,
            $this->domainModelRepo,
            $this->enumRepo,
            $this->queryRepo,
            $this->commandRepo,
            $this->modelStatusRepo,
            $this->linearConfig
        );

        return $query->RelatedIssues($id);
    }

    public function getDocumentorByTypeAndSubjectId(string $type, int $subjectId): DocumentorRM
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);

        $documentorType = DocumentorType::from(strtolower($type));
        switch ($documentorType) {
            case DocumentorType::DOMAIN_MODEL:
                $this->permission->CheckDomainModelId($subjectId);
                break;
            case DocumentorType::ENUM:
                $this->permission->CheckEnumId($subjectId);
                break;
            case DocumentorType::QUERY:
                $this->permission->CheckQueryId($subjectId);
                break;
            case DocumentorType::COMMAND:
                $this->permission->CheckCommandId($subjectId);
                break;
            default:
                $this->permission->CheckModuleId($subjectId);
                break;
        }

        $query = new GetDocumentor(
            $this->entityManager,
            $this->documentorRepository,
            $this->moduleRepository,
            $this->domainModelRepo,
            $this->enumRepo,
            $this->queryRepo,
            $this->commandRepo,
            $this->modelStatusRepo,
            $this->linearConfig
        );

        $result = $query->ByTypeAndSubjectId($documentorType, $subjectId);
        $this->entityManager->flush();

        return $result;
    }
}
