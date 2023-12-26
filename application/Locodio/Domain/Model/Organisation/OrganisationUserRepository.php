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

namespace App\Locodio\Domain\Model\Organisation;

use App\Locodio\Domain\Model\User\User;
use Symfony\Component\Uid\Uuid;

interface OrganisationUserRepository
{
    public function nextIdentity(): Uuid;

    public function save(OrganisationUser $model): ?int;

    public function delete(OrganisationUser $model): bool;

    public function getById(int $id): OrganisationUser;

    public function getByUuid(Uuid $uuid): OrganisationUser;

    public function findByUserAndOrganisation(User $user, Organisation $organisation): ?OrganisationUser;

    /**
     * @return OrganisationUser[]
     */
    public function getByUser(User $user): array;

}
