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

use App\Linear\Application\Query\LinearConfig;
use App\Locodio\Application\Command\Model\CreateSampleProject\CreateSampleProject;
use App\Locodio\Application\Command\Model\CreateSampleProject\CreateSampleProjectHandler;
use App\Locodio\Application\Command\User\CreateAccount\CreateAccount;
use App\Locodio\Application\Command\User\CreateAccountFromInvitation\CreateAccountFromInvitation;
use App\Locodio\Application\Command\User\ForgotPassword\ForgotPassword;
use App\Locodio\Application\Command\User\Register\Register;
use App\Locodio\Application\Command\User\ResetPassword\ResetPasswordHash;
use App\Locodio\Application\Command\User\SendRegistrationMail\SendRegistrationMail;
use App\Locodio\Application\Command\User\SendRegistrationMail\SendRegistrationMailHandler;
use App\Locodio\Application\Command\User\SendResetPasswordMail\SendPasswordLinkMailHandler;
use App\Locodio\Application\Command\User\SendResetPasswordMail\SendResetPasswordLinkMail;
use App\Locodio\Application\CommandBus;
use App\Locodio\Application\QueryBus;
use App\Locodio\Domain\Model\Model\Association;
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\MasterTemplateFork;
use App\Locodio\Domain\Model\Model\ModelSettings;
use App\Locodio\Domain\Model\Model\ModelStatus;
use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\OrganisationUser;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\PasswordResetLink;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserInvitationLink;
use App\Locodio\Domain\Model\User\UserRegistrationLink;
use App\Locodio\Infrastructure\Database\PasswordResetLinkRepository;
use App\Locodio\Infrastructure\Database\UserRepository;
use App\Lodocio\Domain\Model\Project\DocProject;
use App\SsoConnect\ProviderCollection;
use App\SsoConnect\ProviderFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class AuthController extends AbstractController
{
    protected CommandBus $commandBus;
    protected QueryBus $queryBus;
    protected string $logo;
    protected ProviderCollection $providerCollection;

    // ——————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ParameterBagInterface       $parameterBag,
        protected KernelInterface             $appKernel,
        protected Security                    $security,
        protected EntityManagerInterface      $entityManager,
        protected MailerInterface             $mailer,
        protected TranslatorInterface         $translator,
        protected Environment                 $twig,
        protected UserPasswordHasherInterface $passwordEncoder,
    ) {
        // -- create the command bus

        $this->commandBus = new CommandBus(
            security: $security,
            entityManager: $entityManager,
            passwordEncoder: $passwordEncoder,
            mailer: $mailer,
            translator: $translator,
            twig: $twig,
            isolationMode: false,
            userRepository: $entityManager->getRepository(User::class),
            passwordResetLinkRepository: $entityManager->getRepository(PasswordResetLink::class),
            organisationRepository: $entityManager->getRepository(Organisation::class),
            projectRepository: $entityManager->getRepository(Project::class),
            userRegistrationLinkRepository: $entityManager->getRepository(UserRegistrationLink::class),
            masterTemplateRepository: $entityManager->getRepository(MasterTemplate::class),
            masterTemplateForkRepository: $entityManager->getRepository(MasterTemplateFork::class),
            docProjectRepository: $this->entityManager->getRepository(DocProject::class),
            organisationUserRepository: $entityManager->getRepository(OrganisationUser::class),
            userInvitationLinkRepository: $entityManager->getRepository(UserInvitationLink::class),
        );

        // -- create the query bus

        $linearConfig = new LinearConfig('', '', 'false');
        $this->queryBus = new QueryBus(
            security: $security,
            entityManager: $entityManager,
            isolationMode: false,
            userRepository: $entityManager->getRepository(User::class),
            userInvitationLinkRepository: $entityManager->getRepository(UserInvitationLink::class),
            passwordResetLinkRepository: $entityManager->getRepository(PasswordResetLink::class),
            organisationRepository: $entityManager->getRepository(Organisation::class),
            projectRepository: $entityManager->getRepository(Project::class),
            masterTemplateRepository: $entityManager->getRepository(MasterTemplate::class),
            domainModelRepository: $entityManager->getRepository(DomainModel::class),
            documentorRepository: $entityManager->getRepository(Documentor::class),
            linearConfig: $linearConfig
        );

        // -- get the logo

        $this->logo = trim($_ENV["APP_LOGO"]);
        if ($_SERVER['HTTP_HOST'] === 'appfoundry.locod.io') {
            $this->logo = '/app_foundry.svg';
        }

        // -- get the sso providers

        $this->providerCollection = ProviderFactory::makeProviderCollection($_SERVER);
    }

    // ——————————————————————————————————————————————————————————————————————
    // Login
    // ——————————————————————————————————————————————————————————————————————

    #[Route('/sign-in', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // -- get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // -- last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Auth/login.html.twig', [
            'controller_name' => 'AuthController',
            'last_username' => $lastUsername,
            'error' => $error,
            'logo' => $this->logo,
            'app_has_registration' => $_ENV["APP_HAS_REGISTRATION"],
            'theme_color' => $_ENV["APP_THEME_COLOR"],
            'sso_providers' => $this->providerCollection->getProviders(),
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
        return $this->render('Auth/forgot.html.twig', ['logo' => $this->logo, 'theme_color' => $_ENV["APP_THEME_COLOR"],]);
    }

    #[Route('/forgot', name: 'app_forgot_action', methods: ['POST'])]
    public function forgotAction(
        Request         $request,
        ManagerRegistry $registry,
    ): Response {
        if ($this->isCsrfTokenValid('forgot_password', (string)$request->request->get('csrf_token'))) {
            $email = $request->request->get('forgotPasswordEmail');
            $command = new ForgotPassword($email);
            $result = $this->commandBus->userForgotPassword($command);

            if ($result->message == 'reset_link_sent') {
                $sendMailService = new SendPasswordLinkMailHandler(
                    new PasswordResetLinkRepository($registry),
                    new UserRepository($registry),
                    $this->mailer,
                    $this->translator,
                    $this->twig,
                    $translator->trans('no_reply', [], 'mail'),
                    $translator->trans('no_reply_name', [], 'mail')
                );
                $command = new SendResetPasswordLinkMail(
                    'en',
                    $result->uuid,
                    $request->getHost(),
                    $result->signature,
                    $result->verificationCode
                );
                $sendMailService->SendResetLink($command);

                // -- feedback for the template
                $feedback = $translator->trans('email_reset_link', [], 'auth');
                $this->addFlash('success', $feedback . ' ' . $email);
                return $this->redirectToRoute('app_reset_verification', ['signature' => $result->signature]);

            } else {
                $this->addFlash('error', 'email_not_found');
            }
        }
        return $this->render('Auth/forgot.html.twig', [
            'logo' => $this->logo,
            'theme_color' => $_ENV["APP_THEME_COLOR"],
        ]);
    }

    // ——————————————————————————————————————————————————————————————————————
    // Reset password
    // ——————————————————————————————————————————————————————————————————————

    #[Route('/reset-password-verification/{signature}', name: 'app_reset_verification', methods: ['GET'])]
    public function resetVerification(string $signature, Request $request): Response
    {
        $code = '';
        if (false === is_null($request->get('code'))) {
            $code = (int)$request->get('code');
        }
        return $this->render('Auth/reset_verification.html.twig', [
            'logo' => $this->logo,
            'theme_color' => $_ENV["APP_THEME_COLOR"],
            'code' => $code,
            'signature' => $signature,
        ]);
    }

    #[Route('/reset-password/{signature}', name: 'app_reset_form', methods: ['POST'])]
    public function reset(string $signature, Request $request): Response
    {
        $canResetPassword = true;
        $verificationCode = '100000';
        if ($this->isCsrfTokenValid('reset_verification', (string)$request->request->get('csrf_token'))) {
            $verificationCode = (int)$request->get('code');
            $isResetLinkValid = $this->queryBus->isPasswordResetLinkIsValid($signature, $verificationCode);
            if ($isResetLinkValid) {
                $resetLink = $this->queryBus->getPasswordResetLink($signature);
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
            } else {
                $this->addFlash('error', 'link_not_valid');
                $canResetPassword = false;
            }
        } else {
            $this->addFlash('error', 'link_not_valid');
            $canResetPassword = false;
        }

        return $this->render('Auth/reset.html.twig', [
            'canResetPassword' => boolval($canResetPassword),
            'signature' => $signature,
            'logo' => $this->logo,
            'theme_color' => $_ENV["APP_THEME_COLOR"],
            'code' => $verificationCode,
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/set-new-password/{signature}', name: 'app_reset_action', methods: ['POST'])]
    public function resetAction(string $signature, Request $request): Response
    {
        if ($this->isCsrfTokenValid('reset_password', (string)$request->request->get('csrf_token'))) {
            $verificationCode = (int)$request->get('code');
            $password1 = (string)$request->request->get('resetPassword1');
            $password2 = (string)$request->request->get('resetPassword2');
            $canResetPassword = true;
            if ($password1 != $password2) {
                $this->addFlash('warning', 'no_matching_password');
            } else {
                $command = new ResetPasswordHash($signature, $verificationCode, $password1);
                $this->commandBus->resetPasswordWithHash($command);
                return $this->redirectToRoute('app_reset_action_done', ['signature' => $signature]);
            }
        } else {
            $canResetPassword = false;
            $this->addFlash('warning', 'link_not_valid');
        }

        return $this->render('Auth/reset.html.twig', [
            'canResetPassword' => boolval($canResetPassword),
            'signature' => $signature,
            'logo' => $this->logo,
            'theme_color' => $_ENV["APP_THEME_COLOR"],
        ]);
    }

    #[Route('/reset-password-done/{signature}', name: 'app_reset_action_done', methods: ['GET'])]
    public function resetActionDone(string $signature): Response
    {
        return $this->render('Auth/reset_ok.html.twig', [
            'logo' => $this->logo,
            'theme_color' => $_ENV["APP_THEME_COLOR"],
        ]);
    }

    // ——————————————————————————————————————————————————————————————————————
    // Sign Up for a new account
    // ——————————————————————————————————————————————————————————————————————

    #[Route('/sign-up', name: 'app_sign_up', methods: ['GET'])]
    public function register(): Response
    {
        if ($_ENV["APP_HAS_REGISTRATION"] === 'false') {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('Auth/register.html.twig', [
            'logo' => $this->logo,
            'theme_color' => $_ENV["APP_THEME_COLOR"],
            'sso_providers' => $this->providerCollection->getProviders(),
        ]);
    }

    #[Route('/sign-up', name: 'app_register_action', methods: ['POST'])]
    public function registerAction(
        Request         $request,
        ManagerRegistry $registry,
    ): Response {
        if ($_ENV["APP_HAS_REGISTRATION"] === 'false') {
            return $this->redirectToRoute('app_login');
        }

        $honeyPot = $request->request->get('url');
        $signature = hash('sha256', Uuid::v4()->toRfc4122());

        if ($honeyPot === '') {
            if ($this->isCsrfTokenValid('register_user', (string)$request->request->get('csrf_token'))) {
                $command = new Register(
                    organisation: $request->request->get('ti_organisation'),
                    firstname: $request->request->get('ti_firstname'),
                    lastname: $request->request->get('ti_lastname'),
                    email: $request->request->get('ti_email'),
                    password1: $request->request->get('ti_password1'),
                    password2: $request->request->get('ti_password2')
                );
                $result = $this->commandBus->registerAction($command);
                if ($result->message == 'register_link_created') {
                    $sendMailService = new SendRegistrationMailHandler(
                        userRegistrationLinkRepo: $this->entityManager->getRepository(UserRegistrationLink::class),
                        mailer: $this->mailer,
                        translator: $this->translator,
                        twig: $this->twig,
                        fromEmail: $this->translator->trans('no_reply', [], 'mail'),
                        fromName: $this->translator->trans('no_reply_name', [], 'mail')
                    );
                    $command = new SendRegistrationMail(
                        locale: 'en',
                        linkUuid: $result->uuid,
                        host: $request->getHost(),
                        mailToAddress: $result->mailToEmail,
                        mailToName: $result->mailToName,
                        verificationCode: $result->verificationCode,
                    );
                    $sendMailService->SendInvitation($command);
                    $signature = $result->signature;
                }
            } else {
                $this->addFlash('error', $result->message);
                return $this->render('Auth/register_ok.html.twig', ['logo' => $this->logo, 'theme_color' => $_ENV["APP_THEME_COLOR"],]);
            }
        }
        return $this->redirectToRoute('app_register_verification', ['signature' => $signature]);
    }

    #[Route('/sign-up/{signature}', name: 'app_register_verification', methods: ['GET'])]
    public function registerVerification(string $signature, Request $request): Response
    {
        if ($_ENV["APP_HAS_REGISTRATION"] === 'false') {
            return $this->redirectToRoute('app_login');
        }

        $code = '';
        if (false === is_null($request->get('code'))) {
            $code = (int)$request->get('code');
        }

        return $this->render('Auth/verification.html.twig', [
            'logo' => $this->logo,
            'theme_color' => $_ENV["APP_THEME_COLOR"],
            'code' => $code,
            'signature' => $signature,
        ]);
    }

    #[Route('/sign-up/{signature}', name: 'app_register_confirmation', methods: ['POST'])]
    public function registerConfirmation(string $signature, Request $request): Response
    {
        if ($_ENV["APP_HAS_REGISTRATION"] === 'false') {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('user_confirmation', (string)$request->request->get('csrf_token'))) {

            $code = (int)$request->get('code');
            $command = new CreateAccount($signature, $code);
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
                    $this->entityManager->getRepository(ModelStatus::class),
                    $this->entityManager->getRepository(Module::class),
                    $this->entityManager->getRepository(ModelSettings::class),
                );
                $result = $createSampleProjectHandler->go($command);
                $this->entityManager->flush();
                $this->addFlash('success', 'account_created');
                return $this->render('Auth/registration_confirmed.html.twig', [
                    'logo' => $this->logo,
                    'theme_color' => $_ENV["APP_THEME_COLOR"],
                ]);
            } else {
                $this->addFlash('warning', $result->message);
                return $this->redirectToRoute('app_register_verification', ['signature' => $signature]);
            }
        } else {
            $this->addFlash('warning', 'Invalid token');
            return $this->redirectToRoute('app_register_verification', ['signature' => $signature]);
        }
    }

    // ——————————————————————————————————————————————————————————————————————
    // Create an account via an invitation link
    // ——————————————————————————————————————————————————————————————————————

    #[Route('/enroll/{signature}', name: 'app_enroll_verification', methods: ['GET'])]
    public function enrollVerification(string $signature, Request $request): Response
    {
        $code = '';
        if (false === is_null($request->get('code'))) {
            $code = (int)$request->get('code');
        }

        return $this->render('Auth/create_account_verification.html.twig', [
            'logo' => $this->logo,
            'theme_color' => $_ENV["APP_THEME_COLOR"],
            'code' => $code,
            'signature' => $signature,
        ]);
    }

    #[Route('/enroll/{signature}', name: 'app_enroll_verification_action', methods: ['POST'])]
    public function enrollVerificationAction(string $signature, Request $request): Response
    {
        if ($this->isCsrfTokenValid('create_account_verification', (string)$request->request->get('csrf_token'))) {
            $code = (int)$request->get('code');
            $command = new \stdClass();
            $command->signature = $signature;
            $command->verificationCode = $code;
            $isCodeValid = $this->commandBus->validateInvitation($command);
            if ($isCodeValid) {
                return $this->render('Auth/create_account.html.twig', [
                    'logo' => $this->logo,
                    'theme_color' => $_ENV["APP_THEME_COLOR"],
                    'code' => $code,
                    'signature' => $signature,
                ]);
            } else {
                $this->addFlash('warning', 'Invalid link or invalid code.');
                return $this->redirectToRoute('app_enroll_verification', ['signature' => $signature, 'code' => $code]);
            }
        } else {
            $this->addFlash('warning', 'Invalid token');
            return $this->redirectToRoute('app_enroll_verification', ['signature' => $signature]);
        }
    }

    #[Route('/create-account/{signature}', name: 'app_create_account_action', methods: ['POST'])]
    public function createAccountAction(string $signature, Request $request): Response
    {
        if ($this->isCsrfTokenValid('create_account', (string)$request->request->get('csrf_token'))) {
            $code = (int)$request->request->get('code');
            $command = new CreateAccountFromInvitation(
                signature: $signature,
                code: $code,
                firstName: (string)$request->request->get('ti_firstname'),
                lastName: (string)$request->request->get('ti_lastname'),
                password1: (string)$request->request->get('ti_password1'),
                password2: (string)$request->request->get('ti_password2'),
            );
            $result = $this->commandBus->createAccountFromInvitation($command);
            if ($result) {
                $this->addFlash('success', 'User account created.');
                return $this->redirectToRoute('app_login', ['signature' => $signature]);
            } else {
                $this->addFlash('warning', 'Invalid link or invalid code.');
                return $this->redirectToRoute('app_enroll_verification', ['signature' => $signature, 'code' => $code]);
            }
        } else {
            $this->addFlash('warning', 'Invalid token');
            return $this->redirectToRoute('app_enroll_verification', ['signature' => $signature]);
        }
    }

}
