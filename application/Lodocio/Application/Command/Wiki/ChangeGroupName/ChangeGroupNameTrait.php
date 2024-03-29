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

namespace App\Lodocio\Application\Command\Wiki\ChangeGroupName;

trait ChangeGroupNameTrait
{
    /**
     * @throws \Exception
     */
    public function changeGroupName(\stdClass $jsonCommand): bool
    {
        $command = ChangeGroupName::hydrateFromJson($jsonCommand);
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiNodeGroupId($command->getId());
        $handler = new ChangeGroupNameHandler($this->wikiNodeGroupRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
