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

namespace App\Locodio\Domain\Model\User;

use App\Locodio\Domain\Model\Organisation\Organisation;
use Symfony\Component\Uid\Uuid;

interface UserInvitationLinkRepository
{
    public function nextIdentity(): Uuid;

    public function save(UserInvitationLink $model): ?int;

    public function delete(UserInvitationLink $model): bool;

    public function getById(int $id): UserInvitationLink;

    public function getByUuid(Uuid $uuid): UserInvitationLink;

    public function getByCode(string $code): UserInvitationLink;

    public function findByOrganisationAndEmail(Organisation $organisation, string $email): ?UserInvitationLink;

    public function getActiveByOrganisation(Organisation $organisation): array;

}
