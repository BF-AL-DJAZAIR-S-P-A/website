<?php

namespace App\Controller;

use App\Entity\Candidatures;
use App\Entity\Note;
use App\Form\CandidaturesType;
use App\Form\NoteType;
use App\Repository\CandidaturesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/{_locale}/admin/')]
final class CandidaturesController extends AbstractController
{
    #[Route(name: 'app_candidatures_index', methods: ['GET'])]
    public function index(CandidaturesRepository $candidaturesRepository): Response
    {
        return $this->render('candidatures/index.html.twig', [
            'candidatures' => $candidaturesRepository->findAll(),
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
    // Formulaire Candidature
    $form = $this->createForm(CandidaturesType::class, $candidature);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        $this->addFlash('success', 'Candidature mise à jour');
        return $this->redirectToRoute('app_candidatures_edit', ['id' => $candidature->getId()]);
    }

    // Formulaire Note
    $note = $candidature->getNote() ?? new Note();
    if (!$candidature->getNote()) {
        $candidature->setNote($note);
        $note->setCandidature($candidature);
    }

    $formNote = $this->createForm(NoteType::class, $note);
    $formNote->handleRequest($request);

    if ($formNote->isSubmitted() && $formNote->isValid()) {
        $entityManager->persist($note);
        $entityManager->flush();

        $this->addFlash('success', 'Note mise à jour');
        return $this->redirectToRoute('app_candidatures_edit', ['id' => $candidature->getId()]);
    }

    

    return $this->render('candidatures/edit.html.twig', [
        'candidature' => $candidature,
        'form' => $form->createView(),
        'formNote' => $formNote->createView(),
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
