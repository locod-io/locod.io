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

namespace App\Lodocio\Application\Command\Wiki\ChangeStatusNode;

trait ChangeStatusNodeTrait
{
    /**
     * @throws \Exception
     */
    public function changeStatusNode(\stdClass $jsonCommand): bool
    {
        $command = ChangeStatusNode::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiNodeId($command->getId());
        $this->permission->CheckWikiNodeStatusId($command->getWikiNodeStatusId());

        $handler = new ChangeStatusNodeHandler(
            $this->security,
            $this->wikiNodeRepository,
            $this->wikiNodeStatusRepository,
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
