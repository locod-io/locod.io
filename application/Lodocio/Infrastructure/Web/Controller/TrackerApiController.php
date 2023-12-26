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

use App\Figma\FigmaConfig;
use App\Linear\Application\Query\LinearConfig;
use App\Lodocio\Application\TrackerCommandBus;
use App\Lodocio\Application\TrackerQueryBus;
use App\Lodocio\Domain\Model\Project\DocProject;
use App\Lodocio\Domain\Model\Tracker\Tracker;
use App\Lodocio\Domain\Model\Tracker\TrackerNode;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeFile;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeGroup;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatus;
use App\Lodocio\Domain\Model\Tracker\TrackerRelatedProjectDocument;
use App\Lodocio\Infrastructure\Web\Controller\Routes\TrackerRoutes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class TrackerApiController extends AbstractController
{
    // -- routes
    use TrackerRoutes;

    // -- properties
    protected TrackerCommandBus $commandBus;
    protected TrackerQueryBus $queryBus;
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

        if ($_SERVER['FIGMA_USE_GLOBAL'] === 'true') {
            $figmaConfig = new FigmaConfig(
                $_SERVER['FIGMA_ENDPOINT'],
                $_SERVER['FIGMA_API_KEY'],
                $_SERVER['FIGMA_USE_GLOBAL']
            );
        } else {
            $figmaConfig = new FigmaConfig(
                $_SERVER['FIGMA_ENDPOINT'],
                '',
                $_SERVER['FIGMA_USE_GLOBAL']
            );
        }

        $this->queryBus = new TrackerQueryBus(
            $this->security,
            $this->entityManager,
            $isolationMode,
            $linearConfig,
            $this->twig,
            $this->uploadFolder,
            $entityManager->getRepository(DocProject::class),
            $entityManager->getRepository(Tracker::class),
            $entityManager->getRepository(TrackerNode::class),
            $entityManager->getRepository(TrackerNodeGroup::class),
            $entityManager->getRepository(TrackerNodeStatus::class),
            $entityManager->getRepository(TrackerNodeFile::class),
        );

        $this->commandBus = new TrackerCommandBus(
            $this->security,
            $this->entityManager,
            $isolationMode,
            $this->uploadFolder,
            $linearConfig,
            $figmaConfig,
            $entityManager->getRepository(DocProject::class),
            $entityManager->getRepository(Tracker::class),
            $entityManager->getRepository(TrackerNode::class),
            $entityManager->getRepository(TrackerNodeGroup::class),
            $entityManager->getRepository(TrackerNodeStatus::class),
            $entityManager->getRepository(TrackerNodeFile::class),
            $entityManager->getRepository(TrackerRelatedProjectDocument::class),
        );

    }
}
