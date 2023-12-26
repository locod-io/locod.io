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
use App\Locodio\Application\Command\Organisation\UploadProjectLogo\UploadProjectLogo;
use App\Locodio\Application\CommandBus;
use App\Locodio\Application\QueryBus;
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\MasterTemplateFork;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\OrganisationUser;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\PasswordResetLink;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserInvitationLink;
use App\Locodio\Domain\Model\User\UserRegistrationLink;
use App\Locodio\Infrastructure\Web\Controller\traits\user_routes;
use App\Lodocio\Domain\Model\Project\DocProject;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ApiController extends AbstractController
{
    use user_routes;

    protected array $apiAccess;
    protected CommandBus $commandBus;
    protected QueryBus $queryBus;
    protected int $defaultSleep = 300_000;
    protected string $uploadFolder;

    // ———————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected EntityManagerInterface      $entityManager,
        protected ManagerRegistry             $registry,
        protected Security                    $security,
        protected MailerInterface             $mailer,
        protected TranslatorInterface         $translator,
        protected Environment                 $twig,
        protected UserPasswordHasherInterface $passwordEncoder,
        protected KernelInterface             $appKernel
    ) {
        $this->apiAccess = [];
        $isolationMode = false;

        if ($this->appKernel->getEnvironment() == 'dev') {
            $this->apiAccess = array('Access-Control-Allow-Origin' => '*');
            $isolationMode = true;
        }

        if ($_SERVER['LINEAR_USE_GLOBAL'] === 'true') {
            $linearConfig = new LinearConfig(
                $_SERVER['LINEAR_ENDPOINT'],
                $_SERVER['LINEAR_API_KEY'],
                $_SERVER['LINEAR_USE_GLOBAL']
            );
        } else {
            $linearConfig = new LinearConfig(
                $_SERVER['LINEAR_ENDPOINT'],
                '',
                $_SERVER['LINEAR_USE_GLOBAL']
            );
        }

        $this->commandBus = new CommandBus(
            security: $security,
            entityManager: $entityManager,
            passwordEncoder: $passwordEncoder,
            mailer: $mailer,
            translator: $translator,
            twig: $twig,
            isolationMode: $isolationMode,
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
        $this->queryBus = new QueryBus(
            security: $security,
            entityManager: $entityManager,
            isolationMode: $isolationMode,
            userRepository: $entityManager->getRepository(User::class),
            userInvitationLinkRepository: $entityManager->getRepository(UserInvitationLink::class),
            passwordResetLinkRepository: $entityManager->getRepository(PasswordResetLink::class),
            organisationRepository: $entityManager->getRepository(Organisation::class),
            projectRepository: $entityManager->getRepository(Project::class),
            masterTemplateRepository: $entityManager->getRepository(MasterTemplate::class),
            domainModelRepository: $entityManager->getRepository(DomainModel::class),
            documentorRepository: $entityManager->getRepository(Documentor::class),
            linearConfig: $linearConfig,
        );

        $this->uploadFolder = $appKernel->getProjectDir() . '/' . $_SERVER['UPLOAD_FOLDER'] . '/';
    }

    // ———————————————————————————————————————————————————————————————————————————
    // Index
    // ———————————————————————————————————————————————————————————————————————————

    #[Route('/api', name: 'api_index')]
    public function index(): Response
    {
        $response = 'locod.io api';
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // ———————————————————————————————————————————————————————————————————————————
    // User
    // ———————————————————————————————————————————————————————————————————————————

    #[Route('/api/status', name: 'get_api_stack_status', methods: ['GET'])]
    public function getApiStatus(): Response
    {
        $organisationRepo = $this->entityManager->getRepository(Organisation::class);
        $organisationRepo->getAll();
        $response = new \stdClass();
        $response->status = "OK";
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/user/projects', name: 'get_user_projects', methods: ['GET'])]
    public function getUserProjects(): Response
    {
        $response = $this->queryBus->getOrganisationConfigForUser();
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/user/roadmap', name: 'get_user_road_map', methods: ['GET'])]
    public function getUserRoadmap(): Response
    {
        $response = $this->queryBus->getRoadmapsForUser();
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }

    #[Route('/api/user/change-password', methods: ['POST'])]
    public function changePassword(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('user'));
        $response = $this->commandBus->changePassword($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/user/change-profile', methods: ['POST'])]
    public function changeProfile(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('user'));
        $response = $this->commandBus->changeProfile($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/user/change-theme', methods: ['POST'])]
    public function changeTheme(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('theme'));
        $response = $this->commandBus->changeTheme($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/user/c/{email}', name: 'check_user_by_email', methods: ['GET'])]
    public function checkUserByEmail(string $email): Response
    {
        $response = $this->queryBus->checkUserByEmail($email);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/user', name: 'get_session_user', methods: ['GET'])]
    public function getSessionUser(): Response
    {
        $response = $this->queryBus->getUserFromSession();
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // ———————————————————————————————————————————————————————————————————————————
    // Master templates
    // ———————————————————————————————————————————————————————————————————————————

    #[Route('/api/user/master-templates', name: 'get_user_master_templates', methods: ['GET'])]
    public function getUserMasterTemplates(): Response
    {
        $response = $this->queryBus->getMasterTemplatesForUser()->getCollection();
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/user/fork-template/{templateId}', name: 'form_template', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function forkTemplate(Request $request, int $templateId): Response
    {
        $jsonCommand = json_decode($request->request->get('fork-template'));
        $sessionUser = $this->queryBus->getUserFromSession();
        $jsonCommand->userId = $sessionUser->getId();
        $jsonCommand->templateId = intval($templateId);

        $response = $this->commandBus->formTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // ———————————————————————————————————————————————————————————————————————————
    // Organisation
    // ———————————————————————————————————————————————————————————————————————————

    #[Route('/api/organisation/add', methods: ['POST'])]
    public function addOrganisation(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('organisation'));
        $response = $this->commandBus->addOrganisation($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/organisation/order', methods: ['POST'])]
    public function orderOrganisation(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderOrganisations($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/organisation/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getOrganisationById(int $id): Response
    {
        $response = $this->queryBus->getOrganisationById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/organisation/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeOrganisation(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('organisation'));
        $response = $this->commandBus->changeOrganisation($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // ———————————————————————————————————————————————————————————————————————————
    // Project
    // ———————————————————————————————————————————————————————————————————————————

    #[Route('/api/project/add', methods: ['POST'])]
    public function addProject(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('project'));
        $response = $this->commandBus->addProject($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/project/order', methods: ['POST'])]
    public function orderProject(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderProjects($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/project/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeProject(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('project'));
        $response = $this->commandBus->changeProject($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/project/{id}/description', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeProjectDescription(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeProjectDescription($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/project/{id}/related-projects', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeProjectRelatedProjects(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeProjectRelatedProjects($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/project/{id}/related-roadmaps', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeProjectRelatedRoadmaps(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeProjectRelatedRoadmaps($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/project/{id}/upload-logo', requirements: ['id' => '\d+'])]
    public function uploadLogoForProject(int $id, Request $request)
    {
        $response = false;
        $sessionUser = $this->queryBus->getUserFromSession();
        $file = $request->files->get('logoReference');
        if ($file) {
            $command = new UploadProjectLogo(
                $sessionUser->getId(),
                $id,
                $file,
                $this->uploadFolder
            );
            $response = $this->commandBus->uploadLogoForProject($command);
        }
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/project/{id}/logo', requirements: ['id' => '\d+'])]
    public function streamLogoForProject(int $id, Request $request)
    {
        $project = $this->queryBus->getProjectSummaryById($id);
        $file = $this->uploadFolder . $project->getLogo();
        return $this->file($file, '_logo.png', ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
