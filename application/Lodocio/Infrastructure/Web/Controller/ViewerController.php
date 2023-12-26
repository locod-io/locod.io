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
use App\Locodio\Application\Query\Organisation\GetOrganisation;
use App\Locodio\Application\Query\Organisation\GetProject;
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Lodocio\Application\Query\Tracker\GetTracker;
use App\Lodocio\Application\Query\Tracker\GetTrackerFile;
use App\Lodocio\Application\Query\Wiki\GetWiki;
use App\Lodocio\Application\Query\Wiki\GetWikiFile;
use App\Lodocio\Domain\Model\Project\DocProject;
use App\Lodocio\Domain\Model\Tracker\Tracker;
use App\Lodocio\Domain\Model\Tracker\TrackerNode;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeFile;
use App\Lodocio\Domain\Model\Wiki\Wiki;
use App\Lodocio\Domain\Model\Wiki\WikiNode;
use App\Lodocio\Domain\Model\Wiki\WikiNodeFile;
use App\Lodocio\Domain\Model\Wiki\WikiNodeGroup;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatus;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ViewerController extends AbstractController
{
    protected GetOrganisation $getOrganisation;
    protected GetProject $getProject;
    protected GetTracker $getTracker;
    protected GetWiki $getWiki;
    protected string $uploadFolder;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected Security               $security,
        protected KernelInterface        $appKernel,
        protected Environment            $twig,
    ) {
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

        $this->getOrganisation = new GetOrganisation(
            $this->entityManager->getRepository(Organisation::class),
            $this->entityManager->getRepository(Project::class),
            $linearConfig
        );

        $this->getProject = new GetProject(
            $this->entityManager->getRepository(Project::class),
            $this->entityManager->getRepository(DomainModel::class),
            $this->entityManager->getRepository(Documentor::class),
            $linearConfig
        );

        $this->getWiki = new GetWiki(
            $this->entityManager->getRepository(DocProject::class),
            $this->entityManager->getRepository(Wiki::class),
            $this->entityManager->getRepository(WikiNode::class),
            $this->entityManager->getRepository(WikiNodeGroup::class),
            $this->entityManager->getRepository(WikiNodeStatus::class),
            $linearConfig,
            $this->twig,
            $this->uploadFolder,
            $this->entityManager
        );

        $this->getTracker = new GetTracker(
            $this->entityManager->getRepository(DocProject::class),
            $this->entityManager->getRepository(Tracker::class),
            $this->entityManager->getRepository(TrackerNode::class),
            $linearConfig,
            $this->twig,
            $this->uploadFolder,
        );
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/v/{organisationSlug}/{projectSlug}/w/{wikiSlug}', name: "wiki_viewer", methods: ['GET'])]
    public function getWikiViewer(string $organisationSlug, string $projectSlug, string $wikiSlug): Response
    {
        $organisation = $this->getOrganisation->BySlug($organisationSlug);
        $project = $this->getProject->BySlug($projectSlug);
        $wiki = $this->getWiki->BySlug($wikiSlug);
        $user = $this->security->getUser();

        $showContent = false;
        $userHasOrganisationAccess = false;
        foreach ($user->getOrganisations() as $userOrganisation) {
            if ($userOrganisation->getId() === $organisation->getId()) {
                $userHasOrganisationAccess = true;
            }
        }
        if ($wiki->isPublic() || $userHasOrganisationAccess) {
            $showContent = true;
        }

        if (true === $showContent) {
            $wikiContent = $this->getWiki->FullBySlug($wikiSlug);
            $page = $this->renderView('@lodocio/Viewer/viewer-wiki.html.twig', [
                'organisation' => $organisation,
                'project' => $project,
                'wiki' => $wiki,
                'wikiContent' => $wikiContent,
                'userHasOrganisationAccess' => $userHasOrganisationAccess,
                'selectedSlug' => $wikiSlug,
                'project_color' => $project->getColor(),
            ]);
            return new Response($this->minify($page));

        } else {
            return $this->render('@lodocio/Viewer/viewer-404.html.twig', []);
        }
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/v/{organisationSlug}/{projectSlug}/{trackerSlug}', name: "tracker_viewer", methods: ['GET'])]
    public function getTrackerViewer(string $organisationSlug, string $projectSlug, string $trackerSlug): Response
    {
        $organisation = $this->getOrganisation->BySlug($organisationSlug);
        $project = $this->getProject->BySlug($projectSlug);
        $tracker = $this->getTracker->BySlug($trackerSlug);
        $user = $this->security->getUser();

        $showContent = false;
        $userHasOrganisationAccess = false;
        foreach ($user->getOrganisations() as $userOrganisation) {
            if ($userOrganisation->getId() === $organisation->getId()) {
                $userHasOrganisationAccess = true;
            }
        }
        if ($tracker->isPublic() || $userHasOrganisationAccess) {
            $showContent = true;
        }

        if (true === $showContent) {
            $trackerContent = $this->getTracker->FullBySlug($trackerSlug);
            $wiki = $project->getDocProjectRM()->getWikis()->getCollection()[0];
            $page = $this->renderView('@lodocio/Viewer/viewer-tracker.html.twig', [
                'organisation' => $organisation,
                'project' => $project,
                'tracker' => $tracker,
                'trackerContent' => $trackerContent,
                'wiki' => $wiki,
                'userHasOrganisationAccess' => $userHasOrganisationAccess,
                'selectedSlug' => $trackerSlug,
                'project_color' => $project->getColor(),
            ]);
            return new Response($this->minify($page));
        } else {
            return $this->render('@lodocio/Viewer/viewer-404.html.twig', []);
        }
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/f/{projectSlug}/logo', name: "logo_viewer", methods: ['GET'])]
    public function getProjectLogo(string $projectSlug): Response
    {
        $project = $this->getProject->BySlug($projectSlug);
        $file = $this->uploadFolder . $project->getLogo();
        return $this->file($file, '_logo.png', ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/v/{organisationSlug}/{projectSlug}/w/{wikiSlug}/{imageUuid}', name: "wiki_viewer_image", methods: ['GET'])]
    public function getWikiViewerImage(
        string $organisationSlug,
        string $projectSlug,
        string $wikiSlug,
        string $imageUuid,
    ): Response {
        $organisation = $this->getOrganisation->BySlug($organisationSlug);
        $wiki = $this->getWiki->BySlug($wikiSlug);
        $user = $this->security->getUser();

        $showContent = false;
        $userHasOrganisationAccess = false;
        foreach ($user->getOrganisations() as $userOrganisation) {
            if ($userOrganisation->getId() === $organisation->getId()) {
                $userHasOrganisationAccess = true;
            }
        }
        if ($wiki->isPublic() || $userHasOrganisationAccess) {
            $showContent = true;
        }

        if ($showContent) {
            $getWikiFile = new GetWikiFile(
                $this->entityManager->getRepository(WikiNodeFile::class),
            );
            $wikiFile = $getWikiFile->ByUuid($imageUuid);
            $file = $this->uploadFolder . $wikiFile->getSrcPath();
            return $this->file($file, $wikiFile->getOriginalFileName(), ResponseHeaderBag::DISPOSITION_INLINE);
        } else {
            return $this->render('@lodocio/Viewer/viewer-404.html.twig', []);
        }
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/v/{organisationSlug}/{projectSlug}/{trackerSlug}/{imageUuid}', name: "tracker_viewer_image", methods: ['GET'])]
    public function getTrackerViewerImage(
        string $organisationSlug,
        string $projectSlug,
        string $trackerSlug,
        string $imageUuid,
    ): Response {
        $organisation = $this->getOrganisation->BySlug($organisationSlug);
        $tracker = $this->getTracker->BySlug($trackerSlug);
        $user = $this->security->getUser();

        $showContent = false;
        $userHasOrganisationAccess = false;
        foreach ($user->getOrganisations() as $userOrganisation) {
            if ($userOrganisation->getId() === $organisation->getId()) {
                $userHasOrganisationAccess = true;
            }
        }
        if ($tracker->isPublic() || $userHasOrganisationAccess) {
            $showContent = true;
        }

        if (true === $showContent) {
            $getTrackerFile = new GetTrackerFile(
                $this->entityManager->getRepository(TrackerNodeFile::class),
            );
            $trackerFile = $getTrackerFile->ByUuid($imageUuid);
            $file = $this->uploadFolder . $trackerFile->getSrcPath();
            return $this->file($file, $trackerFile->getOriginalFileName(), ResponseHeaderBag::DISPOSITION_INLINE);
        } else {
            return $this->render('@lodocio/Viewer/viewer-404.html.twig', []);
        }

    }

    private function minify(string $html): string
    {
        $search = array(
            '/(\n|^)\/\/(.*?)(\n|$)/',
            '/\<\!--.*?-->/',
            '/\>\s+\</',        # strip whitespaces between tags
            '/(\"|\')\s+\>/',   # strip whitespaces between quotation ("') and end tags
            '/=\s+(\"|\')/');   # strip whitespaces between = "'

        $replace = array("\n", "", "><", "$1>", "=$1");
        $html = preg_replace($search, $replace, $html);
        $html2 = preg_replace_callback(
            '/(<code>.*?<\/code>)|(\n)/s',
            function ($matches) {
                // If the match is within <code> tags, return it unchanged
                if (!empty($matches[1])) {
                    return $matches[1];
                }
                // Otherwise, replace newline with nothing
                return '';
            },
            $html
        );
        $html3 = preg_replace_callback(
            '/(<code>.*?<\/code>)|([\n\t ]{2,})/s',
            function ($matches) {
                // If the match is within <code> tags, return it unchanged
                if (!empty($matches[1])) {
                    return $matches[1];
                }
                // Otherwise, replace consecutive spaces or tabs with a single space
                return ' ';
            },
            $html2
        );

        return $html3;
    }

}
