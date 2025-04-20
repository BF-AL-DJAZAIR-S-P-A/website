<?php

namespace App\Controller;

use App\Entity\Acctualites;
use App\Form\AcctualitesType;
use App\Repository\AcctualitesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/admin/acctualites')]
final class AcctualitesController extends AbstractController
{
    #[Route(name: 'app_acctualites_index', methods: ['GET'])]
    public function index(AcctualitesRepository $acctualitesRepository): Response
    {
        return $this->render('acctualites/index.html.twig', [
            'acctualites' => $acctualitesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_acctualites_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $acctualite = new Acctualites();
        $form = $this->createForm(AcctualitesType::class, $acctualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($acctualite);
            $entityManager->flush();

            return $this->redirectToRoute('app_acctualites_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('acctualites/new.html.twig', [
            'acctualite' => $acctualite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_acctualites_show', methods: ['GET'])]
    public function show(Acctualites $acctualite): Response
    {
        return $this->render('acctualites/show.html.twig', [
            'acctualite' => $acctualite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_acctualites_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Acctualites $acctualite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AcctualitesType::class, $acctualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_acctualites_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('acctualites/edit.html.twig', [
            'acctualite' => $acctualite,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_acctualites_delete', methods: ['POST'])]
    public function delete(Request $request, Acctualites $acctualite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$acctualite->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($acctualite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_acctualites_index', [], Response::HTTP_SEE_OTHER);
    }
}
