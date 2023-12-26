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

use App\Linear\Application\Query\LinearConfig;
use App\Lodocio\Application\Query\Wiki\GetWikiNodeFileTrait;
use App\Lodocio\Application\Query\Wiki\GetWikiNodeStatusTrait;
use App\Lodocio\Application\Query\Wiki\GetWikiNodeTrait;
use App\Lodocio\Application\Query\Wiki\GetWikiTrait;
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
use Twig\Environment;

class WikiQueryBus
{
    use GetWikiTrait;
    use GetWikiNodeFileTrait;
    use GetWikiNodeTrait;
    use GetWikiNodeStatusTrait;

    protected LodocioPermissionService $permission;

    public function __construct(
        protected Security                             $security,
        protected EntityManagerInterface               $entityManager,
        protected bool                                 $isolationMode,
        protected LinearConfig                         $linearConfig,
        protected Environment                          $twig,
        protected string                               $uploadFolder,
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
