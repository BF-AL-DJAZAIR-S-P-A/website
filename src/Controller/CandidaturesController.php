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

    // MAIL
$mail = new Mail();
$mail->setCandidat($candidature);
$mail->setEmail($candidature->getEmail() ?? ''); // pré-remplir email candidat si disponible
$mail->setObjet('Votre candidature chez BF ALDJAZAIR');

$mail->setContenu(
    "Bonjour ".$candidature->getPrenom().",\n Nous vous remercions pour l’intérêt que vous portez à BF ALDJAZAIR et pour le temps consacré à votre candidature.\n Après un examen attentif de votre dossier, nous regrettons de vous informer que votre candidature n’a pas été retenue pour le poste de ".$candidature->getPoste().".\n Nous vous encourageons à postuler à nouveau pour de futures opportunités correspondant à votre profil. \n Nous vous souhaitons plein succès dans vos projets professionnels."
);
$formMail = $this->createForm(MailType::class, $mail);
$formMail->handleRequest($request);

if ($formMail->isSubmitted() && $formMail->isValid()) {
    // rendre le contenu avec Twig
    $contenu = $this->renderView('emails/candidat.html.twig', [
        'candidat' => $candidature,
        'mail' => $mail,
    ]);

    $message = (new \Swift_Message($mail->getObjet()))
        ->setFrom('info@bfaldjazair.com')
        ->setTo($mail->getEmail());

    if ($mail->getCc()) {
        $message->setCc(explode(';', $mail->getCc()));
    }

    $message->setBody($contenu, 'text/html'); // utiliser le template rendu

    try {
        $mailer->send($message);
        $mail->setStatutEnvoi('envoyé');
    } catch (\Exception $e) {
        $mail->setStatutEnvoi('erreur');
    }

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
