<?php

namespace App\Controller;

use App\Entity\Appels;
use App\Form\AppelsType;
use App\Repository\AppelsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin/appels')]
final class AppelsController extends AbstractController
{
    #[Route(name: 'app_appels_index', methods: ['GET'])]
    public function index(AppelsRepository $appelsRepository): Response
    {
        return $this->render('appels/index.html.twig', [
            'appels' => $appelsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_appels_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $appel = new Appels();
        $form = $this->createForm(AppelsType::class, $appel);
        $form->handleRequest($request);
  
        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'), // à définir
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Erreur lors du déplacement du fichier.');
                }
    
                // On remplace l’objet UploadedFile par le nom du fichier dans l’entité
                $appel->setImage($newFilename);
            }


            $entityManager->persist($appel);
            $entityManager->flush();

            return $this->redirectToRoute('app_appels_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('appels/new.html.twig', [
            'appel' => $appel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_appels_show', methods: ['GET'])]
    public function show(Appels $appel): Response
    {
        return $this->render('appels/show.html.twig', [
            'appel' => $appel,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_appels_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Appels $appel, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(AppelsType::class, $appel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'), // à définir
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Erreur lors du déplacement du fichier.');
                }
    
                // On remplace l’objet UploadedFile par le nom du fichier dans l’entité
                $appel->setImage($newFilename);
            }


            $entityManager->flush();

            return $this->redirectToRoute('app_appels_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('appels/edit.html.twig', [
            'appel' => $appel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_appels_delete', methods: ['POST'])]
    public function delete(Request $request, Appels $appel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appel->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($appel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_appels_index', [], Response::HTTP_SEE_OTHER);
    }
}
