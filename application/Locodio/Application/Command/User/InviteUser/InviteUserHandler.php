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

namespace App\Locodio\Application\Command\User\InviteUser;

use App\Locodio\Application\Command\User\SendInvitationMail\SendInvitationMail;
use App\Locodio\Application\Command\User\SendInvitationMail\SendInvitationMailHandler;
use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\Organisation\OrganisationUser;
use App\Locodio\Domain\Model\User\UserInvitationLink;
use App\Locodio\Domain\Model\User\UserInvitationLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use App\Locodio\Domain\Model\User\UserRole;
use App\Locodio\Infrastructure\Database\OrganisationUserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class InviteUserHandler
{
    public function __construct(
        protected UserRepository               $userRepository,
        protected OrganisationRepository       $organisationRepository,
        protected OrganisationUserRepository   $organisationUserRepository,
        protected UserInvitationLinkRepository $invitationLinkRepository,
        protected MailerInterface              $mailer,
        protected TranslatorInterface          $translator,
        protected Environment                  $twig,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function invite(InviteUser $command): bool
    {
        $organisation = $this->organisationRepository->getById($command->getOrganisationId());
        $user = $this->userRepository->findByEmail($command->getEmail());
        if (true === is_null($user)) {

            // -- this is a new user for locod.io, so we send an invitation link

            $verificationCode = random_int(100000, 999999);
            $signature = hash('sha256', $command->getEmail() . $verificationCode . $_SERVER['APP_SECRET']);

            // -- create or update the invitation link

            $invitationLink = $this->invitationLinkRepository->findByOrganisationAndEmail(
                organisation: $organisation,
                email: $command->getEmail(),
            );
            if (true === is_null($invitationLink)) {
                $invitationLink = UserInvitationLink::make(
                    uuid: $this->invitationLinkRepository->nextIdentity(),
                    email: $command->getEmail(),
                    organisation: $organisation,
                    code: $signature,
                );
            } else {
                $invitationLink->setCode($signature);
            }
            $this->invitationLinkRepository->save($invitationLink);

            // -- send the invitation mail

            $sendInvitationCommand = new SendInvitationMail(
                locale: 'en',
                linkUuid: $invitationLink->getUuidAsString(),
                host: $_SERVER['HTTP_HOST'],
                mailToAddress: $command->getEmail(),
                signature: $signature,
                verificationCode: $verificationCode,
            );

            $this->translator->setLocale('en');
            $sendInvitationHandler = new SendInvitationMailHandler(
                invitationLinkRepository: $this->invitationLinkRepository,
                mailer: $this->mailer,
                translator: $this->translator,
                twig: $this->twig,
                fromEmail: $this->translator->trans('no_reply', [], 'mail'),
                fromName: $this->translator->trans('no_reply_name', [], 'mail'),
            );

            $result = $sendInvitationHandler->send($sendInvitationCommand);

        } else {

            $organisationUser = $this->organisationUserRepository->findByUserAndOrganisation(
                user: $user,
                organisation: $organisation,
            );

            if (true === is_null($organisationUser)) {
                $organisationUser = OrganisationUser::make(
                    uuid: $this->organisationUserRepository->nextIdentity(),
                    user: $user,
                    organisation: $organisation,
                );
                $organisationUser->setRoles([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
                $this->organisationUserRepository->save($organisationUser);
            }

            $user->addOrganisation($organisation);
            $organisation->addUser($user);
            $this->userRepository->save($user);
            $this->organisationRepository->save($organisation);
        }

        return true;
    }
}
