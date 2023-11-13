<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Infrastructure\Web\Controller;

use App\Linear\Application\Query\LinearConfig;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Lodocio\Application\ProjectCommandBus;
use App\Lodocio\Application\ProjectQueryBus;
use App\Lodocio\Domain\Model\Project\DocProject;
use App\Lodocio\Infrastructure\Web\Controller\Routes\ProjectRoutes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class DocProjectApiController extends AbstractController
{
    // -- routes
    use ProjectRoutes;

    // -- properties
    protected ProjectCommandBus $commandBus;
    protected ProjectQueryBus $queryBus;
    protected array $apiAccess;
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

        $this->queryBus = new ProjectQueryBus(
            $this->security,
            $this->entityManager,
            $isolationMode,
            $linearConfig,
            $entityManager->getRepository(Project::class),
            $entityManager->getRepository(DocProject::class),
        );

        $this->commandBus = new ProjectCommandBus(
            $this->security,
            $this->entityManager,
            $isolationMode,
            $entityManager->getRepository(Organisation::class),
            $entityManager->getRepository(Project::class),
            $entityManager->getRepository(DocProject::class),
        );
    }

}
