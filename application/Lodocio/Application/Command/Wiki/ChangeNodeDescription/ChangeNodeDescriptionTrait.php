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

namespace App\Lodocio\Application\Command\Wiki\ChangeNodeDescription;

trait ChangeNodeDescriptionTrait
{
    /**
     * @throws \Exception
     */
    public function changeNodeDescription(\stdClass $jsonCommand): bool
    {
        $command = ChangeNodeDescription::hydrateFromJson($jsonCommand);
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiNodeId($command->getId());
        $handler = new ChangeNodeDescriptionHandler($this->wikiNodeRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
