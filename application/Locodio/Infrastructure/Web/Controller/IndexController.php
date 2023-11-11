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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class IndexController extends AbstractController
{
    // ——————————————————————————————————————————————————————————————————
    // Index
    // ——————————————————————————————————————————————————————————————————

    #[Route('/', name: 'app_index')]
    public function index(Request $request, Security $security): Response
    {
        $user = $security->getUser();
        if (!is_null($user)) {
            return $this->redirectToRoute('locodio_app_index');
        } elseif ($_ENV["APP_SHOW_LANDING"] === 'false') {
            return $this->redirectToRoute('app_login');
        } elseif ($_SERVER['HTTP_HOST'] === 'appfoundry.locod.io') {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('Index/index.html.twig', ['app_has_registration' => $_ENV["APP_HAS_REGISTRATION"],]);
    }
}
