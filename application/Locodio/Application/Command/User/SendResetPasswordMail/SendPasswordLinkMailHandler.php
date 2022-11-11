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

namespace App\Locodio\Application\Command\User\SendResetPasswordMail;

use App\Locodio\Domain\Model\User\PasswordResetLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SendPasswordLinkMailHandler
{
    private PasswordResetLinkRepository $resetLinkRepo;
    private UserRepository $userRepo;
    private MailerInterface $mailer;
    private TranslatorInterface $translator;
    private Environment $twig;
    private string $fromEmail;
    private string $fromName;

    // ———————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————

    public function __construct(
        PasswordResetLinkRepository $resetLinkRepo,
        UserRepository              $userRepository,
        MailerInterface             $mailer,
        TranslatorInterface         $translator,
        Environment                 $twig,
        string                      $fromEmail,
        string                      $fromName
    ) {
        $this->resetLinkRepo = $resetLinkRepo;
        $this->userRepo = $userRepository;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->twig = $twig;
    }

    // ———————————————————————————————————————————————————————————————————
    // Commands
    // ———————————————————————————————————————————————————————————————————

    public function SendResetLink(SendResetPasswordLinkMail $command): bool
    {
        $link = $this->resetLinkRepo->getByUuid(Uuid::fromString($command->getLinkUuid()));
        $user = $this->userRepo->getById($link->getUser()->getId());
        $this->translator->setLocale($command->getLocale());
        $mail = new \stdClass();
        $mail->subject = $this->translator->trans('subject_reset_password', [], 'mail');
        $mail->content = $this->translator->trans('content_reset_password', [], 'mail');
        $mail->firstname = $user->getFirstname();
        $mail->link = 'https://' . $command->getHost() . '/reset-password/' . $link->getCode();
        $mail->buttonLabel = $this->translator->trans('button_label_reset_password', [], 'mail');
        $mailTemplate = $this->twig->render('Mail/reset.html.twig', ['mail' => $mail]);

        $message = (new Email())
            ->subject($mail->subject)
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to(new Address($user->getEmail(), $user->getFirstname() . ' ' . $user->getLastname()))
            ->html($mailTemplate);

        $this->mailer->send($message);

        return true;
    }

    public function SendSetLink(SendResetPasswordLinkMail $command): bool
    {
        return true;
    }
}
