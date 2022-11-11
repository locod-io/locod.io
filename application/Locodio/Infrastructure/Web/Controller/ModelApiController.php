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

use App\Locodio\Application\ModelCommandBus;
use App\Locodio\Application\ModelQueryBus;
use App\Locodio\Application\Query\Model\GetTemplate;
use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\Field;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\Model\Relation;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\User;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Collection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

//#[IsGranted('ROLE_USER')]
class ModelApiController extends AbstractController
{
    protected ModelCommandBus $commandBus;
    protected ModelQueryBus $queryBus;
    protected array $apiAccess;
    protected int $defaultSleep = 300_000;

    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected Security               $security,
        protected KernelInterface        $appKernel
    ) {
        $this->apiAccess = [];
        $isolationMode = false;
        if ($this->appKernel->getEnvironment() == 'dev') {
            $this->apiAccess = array('Access-Control-Allow-Origin' => '*');
            $isolationMode = true;
        }

        $this->queryBus = new ModelQueryBus(
            $this->security,
            $this->entityManager,
            $isolationMode,
            $entityManager->getRepository(Project::class),
            $entityManager->getRepository(DomainModel::class),
            $entityManager->getRepository(Enum::class),
            $entityManager->getRepository(Query::class),
            $entityManager->getRepository(Command::class),
            $entityManager->getRepository(Template::class),
            $entityManager->getRepository(MasterTemplate::class),
            $entityManager->getRepository(User::class),
        );

        $this->commandBus = new ModelCommandBus(
            $this->security,
            $this->entityManager,
            $isolationMode,
            $entityManager->getRepository(Project::class),
            $entityManager->getRepository(DomainModel::class),
            $entityManager->getRepository(Enum::class),
            $entityManager->getRepository(EnumOption::class),
            $entityManager->getRepository(Query::class),
            $entityManager->getRepository(Command::class),
            $entityManager->getRepository(Template::class),
            $entityManager->getRepository(Field::class),
            $entityManager->getRepository(Relation::class),
            $entityManager->getRepository(MasterTemplate::class),
            $entityManager->getRepository(User::class),
        );
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Project
    // ——————————————————————————————————————————————————————————————————————————

    #[Route('/api/model/project/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getProjectById(int $id): Response
    {
        $response = $this->queryBus->getProjectById($id);
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/project/{id}/download', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function downloadProjectById(int $id): Response
    {
        $response = $this->queryBus->getProjectById($id);

        //-- complete the templates with full information
        $query = new GetTemplate(
            $this->entityManager->getRepository(Template::class),
            $this->entityManager->getRepository(DomainModel::class),
            $this->entityManager->getRepository(Enum::class),
            $this->entityManager->getRepository(Query::class),
            $this->entityManager->getRepository(Command::class),
        );
        $fullTemplates = [];
        foreach ($response->getTemplates()->getCollection() as $template) {
            $fullTemplates[]=  $query->ById($template->getId());
        }
        $tempResult = json_decode(json_encode($response));
        $tempResult->templates = $fullTemplates;

        // -- convert to json and temp save in the cache
        $tempExportFile = $this->appKernel->getCacheDir().'/'.$response->getUuid().'.json';
        file_put_contents($tempExportFile, json_encode($tempResult));

        return $this->file($tempExportFile, $response->getName().'.json', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

    #[Route('/api/model/project/{id}/create-sample-project', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function createSampleProject(int $id): Response
    {
        $response = $this->commandBus->createExampleProject($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Generate!
    // ——————————————————————————————————————————————————————————————————————————

    #[Route(
        '/api/model/template/{id}/generate/{subjectId}',
        requirements: ['id' => '\d+', 'subjectId' => '\d+'],
        methods: ['GET']
    )]
    public function generateTemplateBySubjectId(
        int $id,
        int $subjectId
    ): Response {
        $response = $this->queryBus->generateTemplateBySubjectId($id, $subjectId);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Get the backend lists
    // ——————————————————————————————————————————————————————————————————————————

    #[Route(
        '/api/model/enum-values',
        requirements: ['id' => '\d+', 'subjectId' => '\d+'],
        methods: ['GET']
    )]
    public function getEnumValues(): Response
    {
        $response = $this->queryBus->getEnumValues();
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Template
    // ——————————————————————————————————————————————————————————————————————————

    #[Route('/api/model/template/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getTemplateById(int $id): Response
    {
        $response = $this->queryBus->getTemplateById($id);
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/template/add', methods: ['POST'])]
    public function addTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('template'));
        $response = $this->commandBus->addTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/template/order', methods: ['POST'])]
    public function orderTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/template/import', methods: ['POST'])]
    public function importTemplatesFromMasterTemplates(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('import'));
        $response = $this->commandBus->importTemplatesFromMasterTemplates($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/template/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('template'));
        $response = $this->commandBus->changeTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/template/{id}/export', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function exportTemplateToMasterTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('export'));
        $response = $this->commandBus->exportTemplateToMasterTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/template/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('template'));
        $response = $this->commandBus->deleteTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Master Template
    // ——————————————————————————————————————————————————————————————————————————

    #[Route('/api/model/master-template/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getMasterTemplateById(int $id): Response
    {
        $response = $this->queryBus->getMasterTemplateById($id);
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/master-template/add', methods: ['POST'])]
    public function addMasterTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('masterTemplate'));
        $response = $this->commandBus->addMasterTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/master-template/order', methods: ['POST'])]
    public function orderMasterTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderMasterTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/master-template/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeMasterTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('masterTemplate'));
        $response = $this->commandBus->changeMasterTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/master-template/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteMasterTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('masterTemplate'));
        $response = $this->commandBus->deleteMasterTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/master-template/browse', methods: ['GET'])]
    public function browseMasterTemplates(): Response
    {
        $response = $this->queryBus->getPublicTemplates();
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Domain Model
    // ——————————————————————————————————————————————————————————————————————————

    #[Route('/api/model/domain-model/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getDomainModelById(int $id): Response
    {
        $response = $this->queryBus->getDomainModelById($id);
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/domain-model/add', methods: ['POST'])]
    public function addDomainModel(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('domainModel'));
        $response = $this->commandBus->addDomainModel($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/domain-model/order', methods: ['POST'])]
    public function orderDomainModel(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderDomainModel($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/domain-model/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeDomainModel(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('domainModel'));
        $response = $this->commandBus->changeDomainModel($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/domain-model/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteDomainModel(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('domainModel'));
        $response = $this->commandBus->deleteDomainModel($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }


    #[Route('/api/model/field/add', methods: ['POST'])]
    public function addField(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('field'));
        $response = $this->commandBus->addField($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/field/order', methods: ['POST'])]
    public function orderFields(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderField($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/field/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeField(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('field'));
        $response = $this->commandBus->changeField($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/field/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteField(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('field'));
        $response = $this->commandBus->deleteField($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/relation/add', methods: ['POST'])]
    public function addRelation(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('relation'));
        $response = $this->commandBus->addRelation($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/relation/order', methods: ['POST'])]
    public function orderRelations(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderRelation($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/relation/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeRelations(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('relation'));
        $response = $this->commandBus->changeRelation($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/relation/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteRelation(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('relation'));
        $response = $this->commandBus->deleteRelation($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Enum
    // ——————————————————————————————————————————————————————————————————————————

    #[Route('/api/model/enum/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getEnumById(int $id): Response
    {
        $response = $this->queryBus->getEnumById($id);
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum/add', methods: ['POST'])]
    public function addEnum(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('enum'));
        $response = $this->commandBus->addEnum($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum/order', methods: ['POST'])]
    public function orderEnum(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderEnum($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeEnum(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('enum'));
        $response = $this->commandBus->changeEnum($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteEnum(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('enum'));
        $response = $this->commandBus->deleteEnum($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum-option/add', methods: ['POST'])]
    public function addEnumOption(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('enum-option'));
        $response = $this->commandBus->addEnumOption($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum-option/order', methods: ['POST'])]
    public function orderEnumOption(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderEnumOption($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum-option/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeEnumOption(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('enum-option'));
        $response = $this->commandBus->changeEnumOption($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum-option/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteEnumOption(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('enum-option'));
        $response = $this->commandBus->deleteEnumOption($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Query
    // ——————————————————————————————————————————————————————————————————————————

    #[Route('/api/model/query/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getQueryById(int $id): Response
    {
        $response = $this->queryBus->getQueryById($id);
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/query/add', methods: ['POST'])]
    public function addQuery(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('query'));
        $response = $this->commandBus->addQuery($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/query/order', methods: ['POST'])]
    public function orderQuery(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderQuery($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/query/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeQuery(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('query'));
        $response = $this->commandBus->changeQuery($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/query/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteQuery(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('query'));
        $response = $this->commandBus->deleteQuery($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Command
    // ——————————————————————————————————————————————————————————————————————————

    #[Route('/api/model/command/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getCommandById(int $id): Response
    {
        $response = $this->queryBus->getCommandById($id);
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/command/add', methods: ['POST'])]
    public function addCommand(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->addCommand($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/command/order', methods: ['POST'])]
    public function orderCommand(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderCommand($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/command/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeCommand(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeCommand($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/command/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteCommand(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteCommand($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
