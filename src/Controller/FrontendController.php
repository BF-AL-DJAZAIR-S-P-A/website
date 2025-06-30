<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Appels;
use App\Repository\AppelsRepository;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Gedmo\Translatable\TranslatableListener;

use App\Entity\Acctualites;
use App\Repository\AcctualitesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
 class FrontendController extends AbstractController
{

    #[Route('/', name: 'redirect_to_locale')]
    public function redirectToLocale(Request $request): RedirectResponse
    {
        $locale = $request->getPreferredLanguage(['fr', 'en', 'ar','it']) ?? 'fr';

        return $this->redirect($request->getBasePath() . '/' . $locale . '/');
    }

    #[Route('/{_locale}/', name: 'app_homepage', requirements: ['_locale' => 'fr|it|en|ar'], defaults: ['_locale' => 'fr'])]
    public function index(AcctualitesRepository $AcctualitesRepository,AppelsRepository $AppelsRepository,Request $request): Response
    {

        $locale = $request->getLocale(); // récupère la locale courante


        $appels = $AppelsRepository->findAllOnlyTranslated($locale);
        $acctualites = $AcctualitesRepository->findAllOnlyTranslated($locale);
       

        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
            'acctualites' => $acctualites,
            'appels' => $appels,
        ]);
    }

    #[Route('/{_locale}/appels-doffres', name: 'app_appels', requirements: ['_locale' => 'fr|it|en|ar'], defaults: ['_locale' => 'fr'])]
    public function appels(AppelsRepository $AppelsRepository,AcctualitesRepository $AcctualitesRepository,Request $request): Response
    {

         $locale = $request->getLocale(); // récupère la locale courante



        $appels = $AppelsRepository->findOnlyTranslated($locale);

     
        $acctualites = $AcctualitesRepository->findBy([], ['date' => 'DESC'], 4);
    

        return $this->render('frontend/appels.html.twig', [
            'controller_name' => 'FrontendController',
            'appels' => $appels,
            'acctualites' => $acctualites,
        ]);
    }
    
    #[Route('/{_locale}/appels-doffres/{id}', name: 'app_appels_show', methods: ['GET'], requirements: ['_locale' => 'fr|it|en|ar'], defaults: ['_locale' => 'fr'])]
    public function appelsShow(Appels $appel,Request $request): Response
    {
        return $this->render('frontend/appelsShow.html.twig', [
            'controller_name' => 'FrontendController',
            'appel' => $appel,
        ]);
    }


    #[Route('/{_locale}/news', name: 'app_acctualites', requirements: ['_locale' => 'fr|it|en|ar'], defaults: ['_locale' => 'fr'])]
    public function acctualites(AcctualitesRepository $AcctualitesRepository,Request $request,): Response
    {
        
        $locale = $request->getLocale(); // récupère la locale courante



        $acctualite = $AcctualitesRepository->findOnlyTranslated($locale);
   

        return $this->render('frontend/acctualites.html.twig', [
            'controller_name' => 'FrontendController',
            'acctualites' => $acctualite,
        ]);
    }

      #[Route('/{_locale}/news/{id}', name: 'app_acctualites_show', methods: ['GET'], requirements: ['_locale' => 'fr|it|en|ar'], defaults: ['_locale' => 'fr'])]
    public function acctualitesShow(Acctualites $acctualites,Request $request): Response
    {
        $locale = $request->getLocale(); // récupère la locale courante

        $acctualites = $AcctualitesRepository->findOnlyTranslatedById($locale,$id);
        return $this->render('frontend/acctualitesShow.html.twig', [
            'controller_name' => 'FrontendController',
            'acctualite' => $acctualites,
        ]);
    }
}
