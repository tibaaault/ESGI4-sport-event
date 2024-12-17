<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Event;
use App\Form\DistanceType;
use App\Service\DistanceCalculator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


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
    public function viewEvent(Request $request, int $id): Response
    {
        $event = $this->entityManager->getRepository(Event::class)
            ->find($id);
            // ->getParticipants();

        // $eventParticipants = $event->getParticipants();

        $form = $this->createForm(DistanceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userLat = $form->get('latitude')->getData();
            $userLon = $form->get('longitude')->getData();

            return $this->redirectToRoute('app_event_distance', ['id'=> $id , 'lat' => $userLat, 'lon' => $userLon]);
        }

        return $this->render(
            'event/view.html.twig',
            [
                'event' => $event,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/events/{id}/distance?lat={lat}&lon={lon}', name: 'app_event_distance', methods: ['GET', 'POST'])]
    public function calculateDistanceToEvent(Event $event, float $lat, float $lon): Response
    {
      
            $eventLat = $event->getLatitude();
            $eventLon = $event->getLongitude();

            $distance = $this->distanceCalculator->calculateDistance($eventLat, $eventLon, $lat, $lon);
        
            return $this->render(
                'event/distance.html.twig',
                
                [
                    'distance' => $distance,
                ]
            );
    }
}
