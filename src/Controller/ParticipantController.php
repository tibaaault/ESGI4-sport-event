<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ParticipantType;
use PharIo\Manifest\Email;
use Symfony\Component\HttpFoundation\Request;

class ParticipantController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/participant', name: 'app_participant')]
    public function index(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    #[Route('/events/{eventId}/participants/new', name: 'app_participant_add_to_event')]
    public function addParticipant(Request $request, int $eventId): Response
    {
        $event = $this->entityManager->getRepository(Event::class)->find($eventId);
        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé.');
        }
    
        $form = $this->createForm(ParticipantType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participant = $form->getData();
            $alreadyBooked = $this->entityManager->getRepository(Participant::class)
                ->findOneBy(['email' => $participant->getEmail(), 'event' => $event]);
            if ($alreadyBooked) {
                $this->addFlash('danger', 'Participant déjà inscrit à cet évènement');
            } else {
                $participant->setEvent($event);
                $this->entityManager->persist($participant);
                $this->entityManager->flush();
                $this->addFlash('success', 'Participant ajouté à l\'évènement');
            }
            return $this->redirectToRoute('app_event_show', ['id' => $eventId]);
        }

        return $this->render('participant/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
