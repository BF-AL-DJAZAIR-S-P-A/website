<?php

namespace App\Controller;

use App\Entity\Candidatures;
use App\Form\CandidaturesType;
use App\Repository\CandidaturesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/{_locale}/candidatures')]
final class CandidaturesController extends AbstractController
{
    #[Route(name: 'app_candidatures_index', methods: ['GET'])]
    public function index(CandidaturesRepository $candidaturesRepository): Response
    {
        return $this->render('candidatures/index.html.twig', [
            'candidatures' => $candidaturesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_candidatures_new', methods: ['GET', 'POST'], requirements: ['_locale' => 'fr|it|en|ar'], defaults: ['_locale' => 'fr'])]
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
                    ->from($form->get('email')->getData())
                    ->to('elm3hdi@gmail.com')
                    ->subject('Nouvelle candidature ' . $form->get('poste')->getData())
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

            



            return $this->redirectToRoute('app_candidatures_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('candidatures/new.html.twig', [
            'candidature' => $candidature,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidatures_show', methods: ['GET'])]
    public function show(Candidatures $candidature): Response
    {
        return $this->render('candidatures/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_candidatures_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidatures $candidature, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidaturesType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_candidatures_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('candidatures/edit.html.twig', [
            'candidature' => $candidature,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidatures_delete', methods: ['POST'])]
    public function delete(Request $request, Candidatures $candidature, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidature->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($candidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_candidatures_index', [], Response::HTTP_SEE_OTHER);
    }
}
