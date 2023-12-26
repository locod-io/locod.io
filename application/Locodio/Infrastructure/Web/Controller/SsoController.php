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

use App\Locodio\Application\Command\User\CreateAccountFromGitHub\CreateAccountFromGitHub;
use App\Locodio\Application\Command\User\CreateAccountFromGitHub\CreateAccountFromGitHubHandler;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\OrganisationUser;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\User;
use App\SsoConnect\ProviderCollection;
use App\SsoConnect\ProviderFactory;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\Github;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class SsoController extends AbstractController
{
    protected ProviderCollection $providerCollection;

    // ——————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————

    public function __construct(
        protected Security                    $security,
        protected EntityManagerInterface      $entityManager,
        protected UserPasswordHasherInterface $passwordEncoder,
    )
    {
        $this->providerCollection = ProviderFactory::makeProviderCollection($_SERVER);
    }

    // ——————————————————————————————————————————————————————————————————————
    // Auth routing for SSO Sign ins
    // ——————————————————————————————————————————————————————————————————————

    #[Route('/auth/{providerName}', name: 'app_login_sso', methods: ['GET'])]
    public function appLoginSSO(Request $request, string $providerName): Response
    {
        if($request->getSchemeAndHttpHost() === 'http://www.locod.io') {
            return $this->redirect('http://locod.io/auth/' . $providerName);
        }

        $providerName = strtolower($providerName);
        $providerConfig = $this->providerCollection->getProviderByName($providerName);
        $redirectUri = $request->getSchemeAndHttpHost() . '/auth/' . $providerName;

        if (false === is_null($providerConfig)) {

            // -- openid integration ------------------------------------------------------------------

            if ($providerConfig->getDriver() === 'openid') {
                $wellKnownUrl = $providerConfig->getIssuerUrl() . '/.well-known/openid-configuration';
                $oidcConfig = json_decode(file_get_contents($wellKnownUrl), true);

                $provider = new GenericProvider([
                    'clientId' => $providerConfig->getClientId(),
                    'clientSecret' => $providerConfig->getClientSecret(),
                    'redirectUri' => $redirectUri,
                    'urlAuthorize' => $oidcConfig['authorization_endpoint'],
                    'urlAccessToken' => $oidcConfig['token_endpoint'],
                    'urlResourceOwnerDetails' => $oidcConfig['userinfo_endpoint'],
                ]);

                if (true === is_null($request->get('code'))) {
                    $scopes = ['openid profile email'];
                    $authorizationUrl = $provider->getAuthorizationUrl(['scope' => $scopes]);
                    $_SESSION['oauth2state_' . $providerName] = $provider->getState();
                    $_SESSION['oauth2pkceCode_' . $providerName] = $provider->getPkceCode();
                    return $this->redirect($authorizationUrl);

                } elseif (empty($_GET['state'])
                    || empty($_SESSION['oauth2state_' . $providerName])
                    || $_GET['state'] !== $_SESSION['oauth2state_' . $providerName]) {

                    if (isset($_SESSION['oauth2state_' . $providerName])) {
                        unset($_SESSION['oauth2state_' . $providerName]);
                    }
                    return new JsonResponse([
                        'provider' => $providerName,
                        'error' => 'Invalid state',
                    ]);

                } else {

                    try {

                        $code = $request->get('code');
                        $accessToken = $provider->getAccessToken('authorization_code', ['code' => $code]);
                        $userInfo = $provider->getResourceOwner($accessToken)->toArray();
                        $email = $userInfo[$providerConfig->getIdentifierKey()];
                        $user = $this->entityManager->getRepository(User::class)->findByEmail($email);
                        if (false === is_null($user)) {

                            //---------------------------------------------------------------------------
                            // -- login this user into the application
                            //---------------------------------------------------------------------------
                            $this->security->login(
                                $user,
                                'security.authenticator.form_login.main',
                                'main'
                            );
                            return $this->redirectToRoute('locodio_app_index');

                        } else {
                            return $this->redirectToRoute('app_sign_up');
                        }

                    } catch (IdentityProviderException $e) {
                        return new JsonResponse([
                            'provider' => $providerName,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // -- github integration ------------------------------------------------------------------

            } elseif ($providerConfig->getDriver() === 'github') {

                $provider = new Github([
                    'clientId' => $providerConfig->getClientId(),
                    'clientSecret' => $providerConfig->getClientSecret(),
                    'redirectUri' => $redirectUri,
                ]);

                if (true === is_null($request->get('code'))) {

                    $options = [
                        'state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
                        'scope' => ['user:email']
                    ];
                    $authorizationUrl = $provider->getAuthorizationUrl($options);
                    $_SESSION['oauth2state_' . $providerName] = $provider->getState();
                    $_SESSION['oauth2pkceCode_' . $providerName] = $provider->getPkceCode();
                    return $this->redirect($authorizationUrl);

                } elseif (empty($_GET['state'])
                    || empty($_SESSION['oauth2state_' . $providerName])
                    || $_GET['state'] !== $_SESSION['oauth2state_' . $providerName]) {

                    if (isset($_SESSION['oauth2state_' . $providerName])) {
                        unset($_SESSION['oauth2state_' . $providerName]);
                    }
                    return new JsonResponse([
                        'provider' => $providerName,
                        'error' => 'Invalid state',
                    ]);

                } else {

                    try {
                        $code = $request->get('code');
                        $token = $provider->getAccessToken('authorization_code', ['code' => $code]);
                        $userInfo = $provider->getResourceOwner($token)->toArray();
                        $email = $userInfo[$providerConfig->getIdentifierKey()];
                        $user = $this->entityManager->getRepository(User::class)->findByEmail($email);
                        if (false === is_null($user)) {

                            // ---------------------------------------------------------------------------
                            // -- login this user into the application
                            // ---------------------------------------------------------------------------
                            if (isset($_SESSION['account_signup_' . $providerName])) {
                                unset($_SESSION['account_signup_' . $providerName]);
                            }
                            $this->security->login(
                                user: $user,
                                authenticatorName: 'security.authenticator.form_login.main',
                                firewallName: 'main'
                            );
                            return $this->redirectToRoute('locodio_app_index');

                        } else {

                            // ---------------------------------------------------------------------------
                            // -- sign up this user into the application
                            // ---------------------------------------------------------------------------
                            if (isset($_SESSION['account_signup_' . $providerName])
                                && $_SESSION['account_signup_' . $providerName] === true) {
                                $command = CreateAccountFromGitHub::make(
                                    name: $userInfo['name'],
                                    email: $email,
                                    company: $userInfo['company'],
                                );
                                $commandHandler = new CreateAccountFromGitHubHandler(
                                    userRepo: $this->entityManager->getRepository(User::class),
                                    organisationRepo: $this->entityManager->getRepository(Organisation::class),
                                    projectRepo: $this->entityManager->getRepository(Project::class),
                                    organisationUserRepository: $this->entityManager->getRepository(OrganisationUser::class),
                                    passwordEncoder: $this->passwordEncoder,
                                );
                                $result = $commandHandler->register($command);
                                $this->entityManager->flush();
                                $user = $this->entityManager->getRepository(User::class)->findByEmail($email);
                                if (false === is_null($user)) {
                                    unset($_SESSION['account_signup_' . $providerName]);
                                    $this->security->login(
                                        user: $user,
                                        authenticatorName: 'security.authenticator.form_login.main',
                                        firewallName: 'main'
                                    );
                                    return $this->redirectToRoute('locodio_app_index');
                                } else {
                                    return $this->redirectToRoute('app_sign_up');
                                }
                            } else {
                                return $this->redirectToRoute('app_sign_up');
                            }
                        }
                    } catch (\Exception $e) {
                        return new JsonResponse([
                            'provider' => $providerName,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            return new JsonResponse([
                'provider' => $providerName,
                'driver' => $providerConfig->getDriver(),
                'error' => 'Driver not supported by this app.',
            ]);
        }
        return new JsonResponse([
            'provider' => $providerName,
            'error' => 'Invalid provider',
        ]);
    }

    // ——————————————————————————————————————————————————————————————————————
    // Auth routing for SSO Sign ups
    // ——————————————————————————————————————————————————————————————————————

    #[Route('/auth/{providerName}/sign-up', name: 'app_register_sso', methods: ['GET'])]
    public function appRegisterSSO(Request $request, string $providerName): Response
    {
        $providerName = strtolower($providerName);
        $providerConfig = $this->providerCollection->getProviderByName($providerName);
        if (false === is_null($providerConfig)) {
            if ($providerConfig->getDriver() === 'github') {

                // ---------------------------------------------------------------------------
                // set a session to remember this is a sign up and redirect to the auth route
                // ---------------------------------------------------------------------------
                $_SESSION['account_signup_' . $providerName] = true;
                return $this->redirectToRoute('app_login_sso', ['providerName' => $providerName]);

            }
            return new JsonResponse([
                'provider' => $providerName,
                'driver' => $providerConfig->getDriver(),
                'error' => 'Driver not supported by this app.',
            ]);
        }
        return new JsonResponse([
            'provider' => $providerName,
            'error' => 'Invalid provider',
        ]);
    }

}
