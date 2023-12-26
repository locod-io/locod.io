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

namespace App\Locodio\Application\Command\User\SendInvitationMail;

use App\Locodio\Domain\Model\User\UserInvitationLinkRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SendInvitationMailHandler
{
    // ———————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————

    public function __construct(
        protected UserInvitationLinkRepository $invitationLinkRepository,
        protected MailerInterface              $mailer,
        protected TranslatorInterface          $translator,
        protected Environment                  $twig,
        protected string                       $fromEmail,
        protected string                       $fromName
    ) {
    }

    // ———————————————————————————————————————————————————————————————————
    // Handler
    // ———————————————————————————————————————————————————————————————————

    public function send(SendInvitationMail $command): bool
    {
        $this->translator->setLocale($command->getLocale());

        $mail = new \stdClass();
        $mail->subject = $this->translator->trans('subject_invitation_password', [], 'mail');
        $mail->link = 'https://' . $command->getHost() . '/enroll/' . $command->getSignature() . '?code=' . $command->getVerificationCode();
        $mail->verificationCode = $command->getVerificationCode();
        $mailTemplate = $this->twig->render('Mail/invitation.html.twig', ['mail' => $mail]);

        $message = (new Email())
            ->subject($mail->subject)
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to(new Address($command->getMailToAddress()))
            ->html($mailTemplate);

        $this->mailer->send($message);

        return true;
    }

}
