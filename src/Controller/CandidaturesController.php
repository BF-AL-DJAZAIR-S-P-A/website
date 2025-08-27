<?php

namespace App\Controller;

use App\Entity\Candidatures;
use App\Entity\Note;
use App\Entity\Statut;
use App\Entity\Mail;
use App\Form\CandidaturesType;
use App\Form\NoteType;
use App\Form\StatutType;
use App\Form\MailType;
use App\Repository\CandidaturesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

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
public function edit(Request $request, Candidatures $candidature,MailerInterface $mailer,TranslatorInterface $translator, EntityManagerInterface $entityManager): Response
{
    // Formulaire Candidature
    $form = $this->createForm(CandidaturesType::class, $candidature);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        $this->addFlash('success', 'Candidature mise à jour');
        return $this->redirectToRoute('app_candidatures_edit', ['id' => $candidature->getId()]);
    }

    // Récupérer la note existante ou en créer une nouvelle
    $note = $candidature->getNote();
    if (!$note) {
        $note = new Note();
        $note->setCandidat($candidature);
        $candidature->setNote($note);
        $entityManager->persist($note); // persiste seulement si elle est nouvelle
    }

    // Formulaire Note
    $formNote = $this->createForm(NoteType::class, $note);
    $formNote->handleRequest($request);

    if ($formNote->isSubmitted() && $formNote->isValid()) {
        $entityManager->flush();

        $this->addFlash('success', 'Note mise à jour');
        return $this->redirectToRoute('app_candidatures_edit', ['id' => $candidature->getId()]);
    }

    // STATUT
    $statut = $candidature->getStatut();
    if (!$statut) {
        $statut = new Statut();
        $statut->setCandidature($candidature);
        $candidature->setStatut($statut);
        $entityManager->persist($statut);
    }
    $formStatut = $this->createForm(StatutType::class, $statut);
    $formStatut->handleRequest($request);
    if ($formStatut->isSubmitted() && $formStatut->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Statut mis à jour');
        return $this->redirectToRoute('app_candidatures_edit', ['id' => $candidature->getId()]);
    }

   
// --- MAIL ---
$mail = new Mail();
$mail->setCandidat($candidature);
$mail->setEmail($candidature->getEmail() ?? '');
$mail->setObjet('Votre candidature chez BF ALDJAZAIR');

// Préremplir (optionnel)
$mail->setCc('mehdi.boumediene@bfaldjazair.com, elm3hdi@gmail.com');

// Contenu selon statut (inchangé)
$statut = $candidature->getStatut();
if ($statut && $statut->getLibelle() === 'refusé') {
    $mail->setContenu($translator->trans('email.refuse', [
        '%prenom%' => $candidature->getPrenom(),
        '%nom%'    => $candidature->getNom(),
        '%poste%'  => $candidature->getPoste(),
    ], 'messages'));
} elseif ($statut && $statut->getLibelle() === 'admis') {
    $mail->setContenu($translator->trans('email.admis', [
        '%prenom%' => $candidature->getPrenom(),
        '%nom%'    => $candidature->getNom(),
        '%poste%'  => $candidature->getPoste(),
    ], 'messages'));
} else {
    $mail->setContenu($translator->trans('email.attente', [
        '%prenom%' => $candidature->getPrenom(),
        '%nom%'    => $candidature->getNom(),
    ], 'messages'));
}

$formMail = $this->createForm(MailType::class, $mail);
$formMail->handleRequest($request);

if ($formMail->isSubmitted() && $formMail->isValid()) {
    $contenuHtml = nl2br($mail->getContenu());

    $message = (new TemplatedEmail())
        ->from(new Address('info@bfaldjazair.com', 'BF AL DJAZAIR - Hiring'))
        ->to($formMail->get('email')->getData())
        ->subject($formMail->get('objet')->getData() . ' - ' . $candidature->getPoste())
        ->htmlTemplate('emails/candidat.html.twig')
        ->context(['contenu' => $contenuHtml]);

    // === CC: normalisation, validation et ajout UNE PAR UNE ===
    $ccString = trim((string) $formMail->get('cc')->getData());
    if ($ccString === '') {
        // fallback sur $mail->getCc() si nécessaire
        $ccString = trim((string) $mail->getCc());
    }

    if ($ccString !== '') {
        // split en acceptant: "a@a.com, b@b.com" ou "a@a.com; b@b.com"
        $parts = preg_split('/\s*[,;]\s*/', $ccString, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($parts as $addr) {
            $addr = trim($addr);
            if ($addr !== '' && filter_var($addr, FILTER_VALIDATE_EMAIL)) {
                $message->addCc($addr); // ajoute directement l’adresse
            } else {
                $this->addFlash('warning', sprintf("Adresse CC invalide ignorée : %s", $addr));
            }
        }
    }

    try {
        $mailer->send($message);
        $mail->setStatutEnvoi('envoyé');
    } catch (\Exception $e) {
        $mail->setStatutEnvoi('erreur: ' . $e->getMessage());
    }

    // Sauvegarde
    $mail->setObjet($formMail->get('objet')->getData());
    $mail->setContenu($formMail->get('contenu')->getData());
    $mail->setCc($formMail->get('cc')->getData());
    $mail->setDateEnvoi(new \DateTime());

    $entityManager->persist($mail);
    $entityManager->flush();

    $this->addFlash('success', 'Mail envoyé');
    return $this->redirectToRoute('app_candidatures_edit', ['id' => $candidature->getId()]);
}


    return $this->render('candidatures/edit.html.twig', [
        'candidature' => $candidature,
        'form' => $form->createView(),
        'formNote' => $formNote->createView(),
        'formStatut' => $formStatut->createView(),
        'formMail' => $formMail->createView(),
        'mail' => $mail,
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
