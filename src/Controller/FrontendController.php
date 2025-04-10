<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Appels;
use App\Repository\AppelsRepository;
use Doctrine\ORM\EntityManagerInterface;

final class FrontendController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
        ]);
    }

    #[Route('/appels-doffres', name: 'app_appels')]
    public function appels(AppelsRepository $AppelsRepository): Response
    {
        $appels = $AppelsRepository->findAll();

        return $this->render('frontend/appels.html.twig', [
            'controller_name' => 'FrontendController',
            'appels' => $appels,
        ]);
    }
    #[Route('/appels-doffres/{id}', name: 'app_appels_show', methods: ['GET'])]
    public function appelsShow(Appels $appel): Response
    {
        return $this->render('frontend/appelsShow.html.twig', [
            'controller_name' => 'FrontendController',
            'appel' => $appel,
        ]);
    }
}
