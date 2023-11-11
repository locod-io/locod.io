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
        if (str_contains($command->getLinearApiKey(), 'lin_api_')) {
            $apiKey = $command->getLinearApiKey();
        } else {
            $apiKey = $organisation->getLinearApiKey();
        }
        $organisation->change(
            $command->getName(),
            $command->getCode(),
            '#' . $command->getColor(),
            $apiKey,
            (string)$slugger->slug($command->getSlug()),
        );
        $this->organisationRepo->save($organisation);

        return true;
    }
}
