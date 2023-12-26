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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ORGANISATION_USER')]
class ApplicationController extends AbstractController
{
    #[IsGranted('ROLE_ORGANISATION_USER')]
    #[Route('/app', name: 'locodio_app_index')]
    public function locodioAppIndex(): Response
    {
        return $this->render('Application/locodio_app.html.twig', ['theme_color' => $_ENV["APP_THEME_COLOR"]]);
    }
}
