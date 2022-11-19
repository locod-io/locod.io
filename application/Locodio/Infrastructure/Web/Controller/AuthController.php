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

namespace App\Locodio\Infrastructure\Web\Controller;

use App\Locodio\Application\Command\Model\CreateSampleProject\CreateSampleProject;
use App\Locodio\Application\Command\Model\CreateSampleProject\CreateSampleProjectHandler;
use App\Locodio\Application\Command\User\CreateAccount\CreateAccount;
use App\Locodio\Application\Command\User\ForgotPassword\ForgotPassword;
use App\Locodio\Application\Command\User\Register\Register;
use App\Locodio\Application\Command\User\ResetPassword\ResetPasswordHash;
use App\Locodio\Application\Command\User\SendRegistrationMail\SendRegistrationMail;
use App\Locodio\Application\Command\User\SendRegistrationMail\SendRegistrationMailHandler;
use App\Locodio\Application\Command\User\SendResetPasswordMail\SendPasswordLinkMailHandler;
use App\Locodio\Application\Command\User\SendResetPasswordMail\SendResetPasswordLinkMail;
use App\Locodio\Application\CommandBus;
use App\Locodio\Application\QueryBus;
use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\MasterTemplateFork;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\Model\Association;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\PasswordResetLink;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserRegistrationLink;
use App\Locodio\Infrastructure\Database\PasswordResetLinkRepository;
use App\Locodio\Infrastructure\Database\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class AuthController extends AbstractController
{
    protected CommandBus $commandBus;
    protected QueryBus $queryBus;

    // ——————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ParameterBagInterface       $parameterBag,
        protected KernelInterface             $appKernel,
        protected Security                    $security,
        protected EntityManagerInterface      $entityManager,
        protected UserPasswordHasherInterface $passwordEncoder,
    ) {
        $this->commandBus = new CommandBus(
            $security,
            $entityManager,
            $passwordEncoder,
            false,
            $entityManager->getRepository(User::class),
            $entityManager->getRepository(PasswordResetLink::class),
            $entityManager->getRepository(Organisation::class),
            $entityManager->getRepository(Project::class),
            $entityManager->getRepository(UserRegistrationLink::class),
            $entityManager->getRepository(MasterTemplate::class),
            $entityManager->getRepository(MasterTemplateFork::class),
        );

        $this->queryBus = new QueryBus(
            $security,
            $entityManager,
            false,
            $entityManager->getRepository(User::class),
            $entityManager->getRepository(PasswordResetLink::class),
            $entityManager->getRepository(Organisation::class),
            $entityManager->getRepository(Project::class),
            $entityManager->getRepository(MasterTemplate::class),
        );
    }

    // ——————————————————————————————————————————————————————————————————————
    // Login
    // ——————————————————————————————————————————————————————————————————————
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Auth/login.html.twig', [
            'controller_name' => 'AuthController',
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // ——————————————————————————————————————————————————————————————————————
    // Logout
    // ——————————————————————————————————————————————————————————————————————
    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        // -- controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    // ——————————————————————————————————————————————————————————————————————
    // Forgot password
    // ——————————————————————————————————————————————————————————————————————
    #[Route('/forgot', name: 'app_forgot', methods: ['GET'])]
    public function forgot(): Response
    {
        return $this->render('Auth/forgot.html.twig', []);
    }

    #[Route('/forgot', name: 'app_forgot_action', methods: ['POST'])]
    public function forgotAction(
        Request             $request,
        MailerInterface     $mailer,
        TranslatorInterface $translator,
        ManagerRegistry     $registry,
        Environment         $twig
    ): Response {
        $email = $request->request->get('forgotPasswordEmail');
        $command = new ForgotPassword($email);
        $result = $this->commandBus->userForgotPassword($command);

        if ($result->message == 'reset_link_sent') {
            $sendMailService = new SendPasswordLinkMailHandler(
                new PasswordResetLinkRepository($registry),
                new UserRepository($registry),
                $mailer,
                $translator,
                $twig,
                $translator->trans('no_reply', [], 'mail'),
                $translator->trans('no_reply_name', [], 'mail')
            );
            $command = new SendResetPasswordLinkMail('en', $result->uuid, $request->getHost());
            $sendMailService->SendResetLink($command);
            // -- feedback for the template
            $feedback = $translator->trans('email_reset_link', [], 'auth');
            $this->addFlash('success', $feedback . ' ' . $email);
        } else {
            $this->addFlash('error', 'email_not_found');
        }

        return $this->render('Auth/forgot.html.twig');
    }

    // ——————————————————————————————————————————————————————————————————————
    // Reset password
    // ——————————————————————————————————————————————————————————————————————
    #[Route('/reset-password/{hash}', name: 'app_reset', methods: ['GET'])]
    public function reset(string $hash): Response
    {
        $resetLink = $this->queryBus->getPasswordResetLink($hash);
        $canResetPassword = true;

        if (!$resetLink->isActive() || $resetLink->isUsed()) {
            $this->addFlash('error', 'link_not_valid');
            $canResetPassword = false;
        } else {
            $now = new \DateTime();
            if ($now >= $resetLink->getExpiresAt()) {
                $this->addFlash('error', 'link_expired');
                $canResetPassword = false;
            }
        }
        return $this->render('Auth/reset.html.twig', ['canResetPassword' => boolval($canResetPassword), 'hash' => $hash]);
    }

    #[Route('/reset-password/{hash}', name: 'app_reset_action', methods: ['POST'])]
    public function resetAction(string $hash, Request $request): Response
    {
        $password1 = $request->request->get('resetPassword1');
        $password2 = $request->request->get('resetPassword2');
        $canResetPassword = true;
        if ($password1 != $password2) {
            $this->addFlash('warning', 'no_matching_password');
        } else {
            $command = new ResetPasswordHash($hash, $password1);
            $this->commandBus->resetPasswordWithHash($command);
            return $this->redirectToRoute('app_reset_action_done', ['hash' => $hash]);
        }
        return $this->render('Auth/reset.html.twig', ['canResetPassword' => boolval($canResetPassword), 'hash' => $hash]);
    }

    #[Route('/reset-password-ok/{hash}', name: 'app_reset_action_done', methods: ['GET'])]
    public function resetActionDone(string $hash): Response
    {
        return $this->render('Auth/reset_ok.html.twig');
    }

    // ——————————————————————————————————————————————————————————————————————
    // Forgot password
    // ——————————————————————————————————————————————————————————————————————
    #[Route('/sign-up', name: 'app_sign_up', methods: ['GET'])]
    public function register(): Response
    {
        return $this->render('Auth/register.html.twig', []);
    }

    #[Route('/sign-up', name: 'app_register_action', methods: ['POST'])]
    public function registerAction(
        Request             $request,
        MailerInterface     $mailer,
        TranslatorInterface $translator,
        ManagerRegistry     $registry,
        Environment         $twig
    ): Response {
        $honeyPot = $request->request->get('url');

        if ($honeyPot === '') {
            $command = new Register(
                $request->request->get('ti_organisation'),
                $request->request->get('ti_firstname'),
                $request->request->get('ti_lastname'),
                $request->request->get('ti_email'),
                $request->request->get('ti_password1'),
                $request->request->get('ti_password2')
            );
            $result = $this->commandBus->registerAction($command);
            if ($result->message == 'register_link_created') {
                $sendMailService = new SendRegistrationMailHandler(
                    $this->entityManager->getRepository(UserRegistrationLink::class),
                    $mailer,
                    $translator,
                    $twig,
                    $translator->trans('no_reply', [], 'mail'),
                    $translator->trans('no_reply_name', [], 'mail')
                );
                $command = new SendRegistrationMail('en', $result->uuid, $request->getHost());
                $sendMailService->SendInvitation($command);
                $this->addFlash('success', 'registration_link_sent');
            } else {
                $this->addFlash('error', $result->message);
            }
        } else {
            $this->addFlash('success', 'registration_link_sent');
        }
        return $this->render('Auth/register_ok.html.twig', []);
    }

    #[Route('/sign-up/{hash}', name: 'app_register_confirmation', methods: ['GET'])]
    public function registerConfirmation(string $hash): Response
    {
        $command = new CreateAccount($hash);
        $result = $this->commandBus->createAccount($command);
        if ($result->message == 'account_created') {
            // -- create dummy project for this account
            $command = new CreateSampleProject($result->projectUuid);
            $createSampleProjectHandler = new CreateSampleProjectHandler(
                $this->entityManager->getRepository(Project::class),
                $this->entityManager->getRepository(DomainModel::class),
                $this->entityManager->getRepository(Attribute::class),
                $this->entityManager->getRepository(Association::class),
                $this->entityManager->getRepository(Enum::class),
                $this->entityManager->getRepository(EnumOption::class),
                $this->entityManager->getRepository(Query::class),
                $this->entityManager->getRepository(Command::class),
                $this->entityManager->getRepository(Template::class),
            );
            $result = $createSampleProjectHandler->go($command);
            $this->entityManager->flush();

            $this->addFlash('success', 'account_created');
        } else {
            $this->addFlash('warning', $result->message);
        }
        return $this->render('Auth/registration_confirmed.html.twig', []);
    }
}
