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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $appel = new Appels();
        $form = $this->createForm(AppelsType::class, $appel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    public function edit(Request $request, Appels $appel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AppelsType::class, $appel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
