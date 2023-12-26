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

namespace App\Lodocio\Application;

use App\Figma\FigmaConfig;
use App\Linear\Application\Query\LinearConfig;
use App\Lodocio\Application\Command\Wiki\AddProjectDocument\AddProjectDocumentTrait;
use App\Lodocio\Application\Command\Wiki\AddWiki\AddWikiTrait;
use App\Lodocio\Application\Command\Wiki\AddWikiNodeStatus\AddWikiNodeStatusTrait;
use App\Lodocio\Application\Command\Wiki\ChangeGroupName\ChangeGroupNameTrait;
use App\Lodocio\Application\Command\Wiki\ChangeNodeDescription\ChangeNodeDescriptionTrait;
use App\Lodocio\Application\Command\Wiki\ChangeNodeName\ChangeNodeNameTrait;
use App\Lodocio\Application\Command\Wiki\ChangeNodeRelatedIssues\ChangeNodeRelatedIssuesTrait;
use App\Lodocio\Application\Command\Wiki\ChangeStatusNode\ChangeStatusNodeTrait;
use App\Lodocio\Application\Command\Wiki\ChangeWiki\ChangeWikiTrait;
use App\Lodocio\Application\Command\Wiki\ChangeWikiFileName\ChangeWikiFileNameTrait;
use App\Lodocio\Application\Command\Wiki\ChangeWikiNodeStatus\ChangeWikiNodeStatusTrait;
use App\Lodocio\Application\Command\Wiki\DeleteProjectDocument\DeleteProjectDocumentTrait;
use App\Lodocio\Application\Command\Wiki\DeleteWiki\DeleteWikiTrait;
use App\Lodocio\Application\Command\Wiki\DeleteWikiFile\DeleteWikiFileTrait;
use App\Lodocio\Application\Command\Wiki\DeleteWikiNode\DeleteWikiNodeTrait;
use App\Lodocio\Application\Command\Wiki\DeleteWikiNodeGroup\DeleteWikiNodeGroupTrait;
use App\Lodocio\Application\Command\Wiki\DeleteWikiNodeStatus\DeleteWikiNodeStatusTrait;
use App\Lodocio\Application\Command\Wiki\OrderWiki\OrderWikiTrait;
use App\Lodocio\Application\Command\Wiki\OrderWikiFile\OrderWikiFileTrait;
use App\Lodocio\Application\Command\Wiki\OrderWikiNodeStatus\OrderWikiNodeStatusTrait;
use App\Lodocio\Application\Command\Wiki\SaveWikiNodeStatusWorkflow\SaveWikiNodeStatusWorkflowTrait;
use App\Lodocio\Application\Command\Wiki\SyncWikiStructure\SyncWikiStructureTrait;
use App\Lodocio\Application\Command\Wiki\UploadFigmaExportImage\UploadFigmaExportImageTrait;
use App\Lodocio\Application\Command\Wiki\UploadWikiFile\UploadWikiFileTrait;
use App\Lodocio\Application\Security\LodocioPermissionService;
use App\Lodocio\Domain\Model\Project\DocProjectRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeFileRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeGroupRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatusRepository;
use App\Lodocio\Domain\Model\Wiki\WikiRelatedProjectDocumentRepository;
use App\Lodocio\Domain\Model\Wiki\WikiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class WikiCommandBus
{
    use AddWikiTrait;
    use ChangeWikiTrait;
    use OrderWikiTrait;
    use DeleteWikiTrait;
    use AddWikiNodeStatusTrait;
    use ChangeWikiNodeStatusTrait;
    use OrderWikiNodeStatusTrait;
    use SaveWikiNodeStatusWorkflowTrait;
    use SyncWikiStructureTrait;
    use ChangeGroupNameTrait;
    use ChangeNodeNameTrait;
    use ChangeNodeDescriptionTrait;
    use ChangeNodeRelatedIssuesTrait;
    use ChangeStatusNodeTrait;
    use DeleteWikiNodeStatusTrait;
    use DeleteWikiNodeTrait;
    use DeleteWikiNodeGroupTrait;
    use UploadWikiFileTrait;
    use ChangeWikiFileNameTrait;
    use DeleteWikiFileTrait;
    use OrderWikiFileTrait;
    use AddProjectDocumentTrait;
    use DeleteProjectDocumentTrait;
    use UploadFigmaExportImageTrait;

    protected LodocioPermissionService $permission;

    public function __construct(
        protected Security                             $security,
        protected EntityManagerInterface               $entityManager,
        protected bool                                 $isolationMode,
        protected string                               $uploadFolder,
        protected LinearConfig                         $linearConfig,
        protected FigmaConfig                          $figmaConfig,
        protected DocProjectRepository                 $docProjectRepository,
        protected WikiRepository                       $wikiRepository,
        protected WikiNodeStatusRepository             $wikiNodeStatusRepository,
        protected WikiNodeRepository                   $wikiNodeRepository,
        protected WikiNodeGroupRepository              $wikiNodeGroupRepository,
        protected WikiNodeFileRepository               $wikiNodeFileRepository,
        protected WikiRelatedProjectDocumentRepository $documentRepository
    ) {
        $this->permission = new LodocioPermissionService(
            $security->getUser(),
            $entityManager,
            $this->isolationMode
        );
    }
}
