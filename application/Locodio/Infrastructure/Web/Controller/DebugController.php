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

namespace App\Locodio\Infrastructure\Web\Controller;

use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DebugController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/debug/organisation', name: "debugOrganisation", methods: ['GET'], condition: '%kernel.debug% === 1')]
    public function getUserForAnOrganisation(Security $security, EntityManagerInterface $entityManager): Response
    {
        $response = 'true';
        $organisationRepo = $entityManager->getRepository(Organisation::class);
        $userRepo = $entityManager->getRepository(User::class);
        $organisation = $organisationRepo->getById(2);
        $response = $userRepo->getByOrganisation($organisation);

        return new JsonResponse($response);
    }

    //    #[Route('/get-warmth-and-love-token', name: "sendWarmthAndLove", methods: ['GET'])]
    //    public function getWarmthAndLoveToken()
    //    {
    //        $refreshToken = "dgwAQT_FW0xJC5Vvqk6NqTAz4HwOcRCc76WgRtB2a1nOQ_A?CZxbRKjwxsH4t5LO";
    //
    //    }


    #[Route('/send-warmth-and-love', name: "sendWarmthAndLove", methods: ['GET'])]
    public function sendWarmthAndLove()
    {

        // 7ec364f5-04e1-47c3-817f-fa859d35ff2d

        $endpoint = "https://live.api.prod.buffup.net/streams/7ec364f5-04e1-47c3-817f-fa859d35ff2d/reactions/send";
        $symbols = [
            "47a60301-1b27-41d8-8589-14fcd0f2c830",
            "f794e3e5-27f2-481b-ac51-503f423081e6",
            "4335c466-529a-416b-b6e5-cfc034679dc4",
            "4f30a905-da69-4479-8c78-95b8ea1267a8",
            "9c828c2e-7fef-403f-936f-18234487ea4c",
            "66054a13-4e35-4e76-aae2-72bc34440597",
        ];
        $bearerToken = "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3MDMxODM1NTIsImlhdCI6MTcwMzE3Mjc1MiwibmJmIjoxNzAzMTcyNzUyLCJ1dWlkIjoiMTViNTMwZjYtOTEzNS00ZTAwLWI3MDEtMzM1OTFkZGQ1OWJiIiwic2NvcGVzIjpbXSwicm9sZXMiOlsicHJpdmlsZWdlZCJdLCJkaXNwbGF5X25hbWUiOiJLdWxhczQ1NzIiLCJwcm9maWxlX3BpY3R1cmUiOiIiLCJpZGVudGl0eV90eXBlIjoidXNlciIsInBhc3Nwb3J0SUQiOm51bGwsImJyb2FkY2FzdGVySUQiOiJ2cnQifQ.MzGg4ZE2nvnRUtoSSAbekxkbuoc--hImxIFosILGz160JkIRu5H105Jx9h1VicxzQUGNh2XuZ3QYSYAUSe_rDFb_NRVvzgY3bDTJa24FHwEZcsiZQA-38E5rtDub8ZGFtkWyEkxi242il14majSa0qD-LSyZes-KgrU_TXUi2mPZ7jE8IQz13tiXms1TN8e8Z1g1kDGRSdtQ8L4ARBoZeWpG_SwwfRZjUXUV8AsmE3QJeJhjOyJc0hXuI15CIKD0pyO92JUfNpVyN9r_8tcr_5b9kkbO0TCl1fzZ3trGhwdrHy-WAj5hijO3j4Il6Qg-mlTm90MrGORdM2G4wxnNVmoLfkYRot28iJZmSiQgaq39Wu9GIg1HtRJSpJ3OsJ9tTKlPqkOwglMJo3aTPak57qeaGCLdRWf6Bhksc5_UdbbQUScBRZj5IT_JagVgARR38WhIBflaAfTou9wgnEBYAl7c8YJn_ilEsrMc4VHLEtavZ88Zw9hk4U9LDgT3qlVizFQ0zv1VP07-NIvxqGTmA2GRfpS_z5tCxXOaQ2CKrxltZNBgMBRrV05oCid5nMFMKShOdetup0UTmM80XMJ6SoqNnJv8mDV-3sW0nF2Xr1fxmi9hcB6E14EL39t1KiAez09xLq2PFMhtqm605WvuxYGdNcFMOWhYZdKFR5my5ls";
        $additionalHeaders = ['X-Buffup-Broadcaster-Id' => 'vrt'];
        $client = new \GuzzleHttp\Client();
        for($i = 0; $i < 1000; $i++) {
            $response = $client->post($endpoint, [
                'json' => $symbols,
                'headers' => array_merge(
                    [
                        'Authorization' => 'Bearer ' . $bearerToken,
                        'Content-Type' => 'application/json',
                    ],
                    $additionalHeaders
                ),
            ]);
            echo $response->getStatusCode() . "\n";
            usleep(10);
        }
        dd($response);
        exit;
    }

}
