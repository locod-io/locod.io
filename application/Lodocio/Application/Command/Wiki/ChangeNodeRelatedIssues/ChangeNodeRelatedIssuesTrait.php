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

namespace App\Lodocio\Application\Command\Wiki\ChangeNodeRelatedIssues;

trait ChangeNodeRelatedIssuesTrait
{
    /**
     * @throws \Exception
     */
    public function changeNodeRelatedIssues(\stdClass $jsonCommand): bool
    {
        $command = ChangeNodeRelatedIssues::hydrateFromJson($jsonCommand);
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiNodeId($command->getId());
        $handler = new ChangeNodeRelatedIssuesHandler($this->wikiNodeRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
