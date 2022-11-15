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

namespace App\Locodio\Application\Command\User\Register;

use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserRegistrationLink;
use App\Locodio\Domain\Model\User\UserRegistrationLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterHandler
{
    public function __construct(
        protected UserRegistrationLinkRepository $userRegistrationLinkRepo,
        protected UserRepository                 $userRepo,
        protected UserPasswordHasherInterface    $passwordEncoder
    ) {
    }

    public function Register(Register $command): \stdClass
    {
        $result = new \stdClass();
        $result->message = '';

        // -- check on email
        try {
            $user = $this->userRepo->getByEmail($command->getEmail());
            $result->message = 'registration_email_already_registered';
            return $result;
        } catch (EntityNotFoundException $exception) {
        }

        // -- check on pwd
        if (!$command->isPasswordValid()) {
            $result->message = 'registration_password_not_valid';
            return $result;
        }

        // -- register the user
        $dummyUser = User::make(
            $this->userRepo->nextIdentity(),
            $command->getEmail(),
            $command->getFirstname(),
            $command->getLastname(),
            []
        );
        $registerLink = UserRegistrationLink::make(
            $this->userRegistrationLinkRepo->nextIdentity(),
            $command->getEmail(),
            $command->getOrganisation(),
            $command->getFirstname(),
            $command->getLastname(),
            $this->passwordEncoder->hashPassword($dummyUser, $command->getPassword1())
        );
        $this->userRegistrationLinkRepo->save($registerLink);
        $result->uuid = $registerLink->getUuid()->toRfc4122();
        $result->message = 'register_link_created';
        return $result;
    }
}
