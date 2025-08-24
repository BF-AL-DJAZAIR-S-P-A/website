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
use App\Entity\Candidatures;
use App\Form\CandidaturesType;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;



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
public function acctualitesShow(int $id, Request $request, AcctualitesRepository $acctualitesRepository): Response
{
    $locale = $request->getLocale();

    $acctualite = $acctualitesRepository->findOnlyTranslatedById($locale, $id);

    if (!$acctualite) {
        throw $this->createNotFoundException('Actualité non trouvée.');
    }

    return $this->render('frontend/acctualitesShow.html.twig', [
        'controller_name' => 'FrontendController',
        'acctualite' => $acctualite,
    ]);
}

     #[Route('/{_locale}/application', name: 'app_application', methods: ['GET', 'POST'], requirements: ['_locale' => 'fr|it|en|ar'], defaults: ['_locale' => 'fr'])]
    public function new(Request $request, EntityManagerInterface $entityManager,MailerInterface $mailer, SluggerInterface $slugger): Response
    {
         $locale = $request->getLocale(); // récupère la locale courante

        $candidature = new Candidatures();
        $form = $this->createForm(CandidaturesType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

               $cvFile = $form->get('cv')->getData();

            if ($cvFile) {
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cvFile->guessExtension();
    
                try {
                    $cvFile->move(
                        $this->getParameter('uploads_directory'), // à définir
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Erreur lors du déplacement du fichier.');
                }
    
                // On remplace l’objet UploadedFile par le nom du fichier dans l’entité
                $candidature->setCv($newFilename);

                
            }

              $email = (new TemplatedEmail())
                    ->from(new Address('info@bfaldjazair.com', 'BF AL DJAZAIR - Hiring'))
                    ->to('elm3hdi@gmail.com')
                    ->subject('New application received for the position of ' . $form->get('poste')->getData())
                    ->htmlTemplate('emails/candidature.html.twig')
                    ->context([
                        'poste' => $form->get('poste')->getData(),
                        'experience' => $form->get('experience')->getData(),
                        'nom' => $form->get('nom')->getData(),
                        'prenom' => $form->get('prenom')->getData(),
                        'datenaissance' => $form->get('datenaissance')->getData(),
                        'ville' => $form->get('ville')->getData(),
                        'adresse_email' => $form->get('email')->getData(),
                        'tel' => $form->get('tel')->getData(),
                        'lettre' => $form->get('lettre')->getData(),
                    ])
                    ->attachFromPath(
                                    $this->getParameter('uploads_directory') . '/' . $newFilename,
                                    $cvFile->getClientOriginalName()
                                );

                // Envoi
                $mailer->send($email);

            $entityManager->persist($candidature);
            $entityManager->flush();

            



            return $this->redirectToRoute('app_application', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('candidatures/new.html.twig', [
            'candidature' => $candidature,
            'form' => $form,
        ]);
    }
}
