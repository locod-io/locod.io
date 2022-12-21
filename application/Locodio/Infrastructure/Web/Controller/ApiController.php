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

use App\Locodio\Application\Command\Organisation\UploadProjectLogo\UploadDocumentorImage;
use App\Locodio\Application\Command\Organisation\UploadProjectLogo\UploadProjectLogo;
use App\Locodio\Application\CommandBus;
use App\Locodio\Application\QueryBus;
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\MasterTemplateFork;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\PasswordResetLink;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserRegistrationLink;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

//#[IsGranted('ROLE_USER')]
class ApiController extends AbstractController
{
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
        protected UserPasswordHasherInterface $passwordEncoder,
        protected KernelInterface             $appKernel
    ) {
        $this->apiAccess = [];
        $isolationMode = false;
        if ($this->appKernel->getEnvironment() == 'dev') {
            $this->apiAccess = array('Access-Control-Allow-Origin' => '*');
            $isolationMode = true;
        }
        $this->commandBus = new CommandBus(
            $security,
            $entityManager,
            $passwordEncoder,
            $isolationMode,
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
            $isolationMode,
            $entityManager->getRepository(User::class),
            $entityManager->getRepository(PasswordResetLink::class),
            $entityManager->getRepository(Organisation::class),
            $entityManager->getRepository(Project::class),
            $entityManager->getRepository(MasterTemplate::class),
            $entityManager->getRepository(DomainModel::class),
            $entityManager->getRepository(Documentor::class),
        );

        $this->uploadFolder = $appKernel->getProjectDir() .'/'.$_SERVER['UPLOAD_FOLDER'].'/';
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

    #[Route('/api/user/projects', name: 'get_user_projects', methods: ['GET'])]
    public function getUserProjects(): Response
    {
        $response = $this->queryBus->getOrganisationConfigForUser();
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
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
        $file = $this->uploadFolder.$project->getLogo();
        return $this->file($file, '_logo.png', ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
