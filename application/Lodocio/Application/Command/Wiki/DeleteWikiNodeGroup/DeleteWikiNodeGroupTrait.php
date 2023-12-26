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

namespace App\Lodocio\Application\Command\Wiki\DeleteWikiNodeGroup;

trait DeleteWikiNodeGroupTrait
{
    /**
     * @throws \Exception
     */
    public function deleteWikiNodeGroup(\stdClass $jsonCommand): bool
    {
        $command = DeleteWikiNodeGroup::hydrateFromJson($jsonCommand);
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiNodeGroupId($command->getId());
        $handler = new DeleteWikiNodeGroupHandler(
            $this->wikiRepository,
            $this->wikiNodeRepository,
            $this->wikiNodeGroupRepository,
        );
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
