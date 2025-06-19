<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Appels;
use App\Repository\AppelsRepository;

use App\Entity\Acctualites;
use App\Repository\AcctualitesRepository;
use Doctrine\ORM\EntityManagerInterface;

final class FrontendController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(AcctualitesRepository $AcctualitesRepository,AppelsRepository $AppelsRepository): Response
    {
       
        $acctualites = $AcctualitesRepository->findBy([], ['date' => 'DESC'], 4);
        $appels = $AppelsRepository->findBy([], ['date' => 'DESC'], 4);

        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
            'acctualites' => $acctualites,
            'appels' => $appels,
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


    #[Route('/news', name: 'app_acctualites')]
    public function acctualites(AcctualitesRepository $AcctualitesRepository): Response
    {
        $acctualites = $AcctualitesRepository->findAll();

        return $this->render('frontend/acctualites.html.twig', [
            'controller_name' => 'FrontendController',
            'acctualites' => $acctualites,
        ]);
    }

      #[Route('/news/{id}', name: 'app_acctualites_show', methods: ['GET'])]
    public function acctualitesShow(Acctualites $acctualites): Response
    {
        return $this->render('frontend/acctualitesShow.html.twig', [
            'controller_name' => 'FrontendController',
            'acctualite' => $acctualites,
        ]);
    }
}
