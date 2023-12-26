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

namespace App\Locodio\Application\Command\User\SendRegistrationMail;

use App\Locodio\Domain\Model\User\UserRegistrationLinkRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SendRegistrationMailHandler
{
    // ———————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————

    public function __construct(
        protected UserRegistrationLinkRepository $userRegistrationLinkRepo,
        protected MailerInterface                $mailer,
        protected TranslatorInterface            $translator,
        protected Environment                    $twig,
        protected string                         $fromEmail,
        protected string                         $fromName
    ) {
    }

    // ———————————————————————————————————————————————————————————————————
    // Commands
    // ———————————————————————————————————————————————————————————————————

    public function SendInvitation(SendRegistrationMail $command): bool
    {
        $link = $this->userRegistrationLinkRepo->getByUuid(Uuid::fromString($command->getLinkUuid()));
        $this->translator->setLocale($command->getLocale());

        $mail = new \stdClass();
        $mail->firstname = $link->getFirstname();
        $mail->subject = $this->translator->trans('subject_registration', [], 'mail');
        $mail->code = $command->getVerificationCode();
        $mail->link = 'https://' . $command->getHost() . '/sign-up/' . $link->getCode(). '?code='.$command->getVerificationCode();
        $mailTemplate = $this->twig->render('Mail/registration.html.twig', ['mail' => $mail]);

        $message = (new Email())
            ->subject($mail->subject)
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to(new Address($link->getEmail(), $link->getFirstname() . ' ' . $link->getLastname()))
            ->html($mailTemplate);

        $this->mailer->send($message);

        return true;
    }
}
