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

namespace App\Locodio\Application\Command\Organisation\ChangeOrganisation;

use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ChangeOrganisationHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected OrganisationRepository $organisationRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ChangeOrganisation $command): bool
    {
        $slugger = new AsciiSlugger();
        $organisation = $this->organisationRepo->getById($command->getId());

        // -- linear api key
        if (str_contains($command->getLinearApiKey(), 'lin_api_')) {
            $apiKey = $command->getLinearApiKey();
        } else {
            $apiKey = $organisation->getLinearApiKey();
        }
        // -- figma api key
        if (str_contains($command->getFigmaApiKey(), 'figd_')) {
            $figmaApiKey = $command->getFigmaApiKey();
        } else {
            $figmaApiKey = $organisation->getFigmaApiKey();
        }

        // -- change the organisation
        $organisation->change(
            $command->getName(),
            $command->getCode(),
            '#' . $command->getColor(),
            $apiKey,
            $figmaApiKey,
            (string)$slugger->slug($command->getSlug()),
        );
        $this->organisationRepo->save($organisation);

        return true;
    }
}
