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
use App\Locodio\Application\ModelCommandBus;
use App\Locodio\Application\ModelQueryBus;
use App\Locodio\Domain\Model\Model\Association;
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\ModelSettings;
use App\Locodio\Domain\Model\Model\ModelStatus;
use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Infrastructure\Web\Controller\traits\generate_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\linear_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\linearRoutes;
use App\Locodio\Infrastructure\Web\Controller\traits\lists_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\model_association_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\model_attribute_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\model_command_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\model_documentor_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\model_domain_model_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\model_enum_option_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\model_enum_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\model_master_template_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\model_module_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\model_query_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\model_settings_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\model_status_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\model_template_routes;
use App\Locodio\Infrastructure\Web\Controller\traits\organisation_project_routes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

//#[IsGranted('ROLE_ORGANISATION_USER')]
class ModelApiController extends AbstractController
{
    // -- traits for the routes
    use organisation_project_routes;
    use generate_routes;
    use lists_routes;
    use model_template_routes;
    use model_master_template_routes;
    use model_domain_model_routes;
    use model_attribute_routes;
    use model_association_routes;
    use model_enum_routes;
    use model_enum_option_routes;
    use model_query_routes;
    use model_command_routes;
    use model_settings_routes;
    use model_status_routes;
    use model_module_routes;
    use model_documentor_routes;
    use linear_routes;

    // -- properties
    protected ModelCommandBus $commandBus;
    protected ModelQueryBus $queryBus;
    protected array $apiAccess;
    protected int $defaultSleep = 300_000;
    protected string $uploadFolder;

    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected Security               $security,
        protected KernelInterface        $appKernel,
        protected Environment            $twig,
    ) {
        $this->apiAccess = [];
        $isolationMode = false;
        if ($this->appKernel->getEnvironment() == 'dev') {
            $this->apiAccess = array('Access-Control-Allow-Origin' => '*');
            $isolationMode = true;
        }

        $this->uploadFolder = $appKernel->getProjectDir() . '/' . $_SERVER['UPLOAD_FOLDER'] . '/';

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

        $this->queryBus = new ModelQueryBus(
            $this->security,
            $this->entityManager,
            $isolationMode,
            $entityManager->getRepository(Organisation::class),
            $entityManager->getRepository(Project::class),
            $entityManager->getRepository(DomainModel::class),
            $entityManager->getRepository(Enum::class),
            $entityManager->getRepository(Query::class),
            $entityManager->getRepository(Command::class),
            $entityManager->getRepository(Template::class),
            $entityManager->getRepository(MasterTemplate::class),
            $entityManager->getRepository(User::class),
            $entityManager->getRepository(ModelStatus::class),
            $entityManager->getRepository(Module::class),
            $entityManager->getRepository(Documentor::class),
            $this->twig,
            $this->uploadFolder,
            $linearConfig
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
            $entityManager->getRepository(Attribute::class),
            $entityManager->getRepository(Association::class),
            $entityManager->getRepository(MasterTemplate::class),
            $entityManager->getRepository(User::class),
            $entityManager->getRepository(ModelSettings::class),
            $entityManager->getRepository(ModelStatus::class),
            $entityManager->getRepository(Module::class),
            $entityManager->getRepository(Documentor::class),
        );
    }

}
