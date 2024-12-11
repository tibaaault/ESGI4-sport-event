<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Event;
use App\Entity\Participant;
use App\Service\DistanceCalculator;
use Symfony\Component\HttpFoundation\JsonResponse;


class EventController extends AbstractController
{   
    public function __construct(private EntityManagerInterface $entityManager, private DistanceCalculator $distanceCalculator)
    {
        $this->distanceCalculator = $distanceCalculator;
    }


    #[Route('/', name: 'app_events')]
    public function listEvents(): Response
    {
        $events = $this->entityManager->getRepository(Event::class)
            ->findAll();
        


        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/events/{id}', name: 'app_event_show')]
    public function viewEvent(int $id): Response
    {
        $event = $this->entityManager->getRepository(Event::class)
            ->find($id)
            ->getParticipants();

        return $this->render(
            'event/view.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/events/{id}/distance?lat={lat}&lon={lon}', name: 'app_event_distance', methods: ['GET'])]
    public function calculateDistanceToEvent(int $id, float $lat, float $lon): JsonResponse
    {
        $event = $this->entityManager->getRepository(Event::class)
        ->find($id);

        if (!$event) {
            return $this->json(['error' => 'Event not found'], 404);
        }

        $eventLat = $event->getLocation();

        $distance = $this->distanceCalculator->calculateDistance($lat, $lon, $eventLat, $eventLat);

        return $this->json([
            'event_id' => $id,
            'distance_km' => $distance,
        ]);
    }

    
}
