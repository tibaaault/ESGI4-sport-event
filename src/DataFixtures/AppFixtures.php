<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Event;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $eventsLocations = [
            '1 Place Rihour, 59800 Lille' => ['latitude' => 50.636565, 'longitude' => 3.063528],
            '19 Rue de Béthune, 59800 Lille' => ['latitude' => 50.634997, 'longitude' => 3.063928],
            '35 Boulevard de la Liberté, 59800 Lille' => ['latitude' => 50.632936, 'longitude' => 3.061400],
            '10 Rue Nationale, 59800 Lille' => ['latitude' => 50.634151, 'longitude' => 3.059992],
        ];

        $participantsLocations = [
            '44 Avenue du Président Kennedy, 59000 Lille' => ['latitude' => 50.642428, 'longitude' => 3.065582],
            '3 Rue des Tanneurs, 59000 Lille' => ['latitude' => 50.636045, 'longitude' => 3.066320],
            '1 Rue de la Monnaie, 59800 Lille' => ['latitude' => 50.640566, 'longitude' => 3.062989],
            '150 Rue Pierre Mauroy, 59800 Lille' => ['latitude' => 50.633370, 'longitude' => 3.065895],
        ];

        foreach ($eventsLocations as $location => $coordinates) {
            $event = new Event();
            $event->setName($faker->sentence);
            $event->setDate($faker->dateTimeBetween('now', '+1 month'));
            $event->setLocation($location);
            $event->setLatitude($coordinates['latitude']);
            $event->setLongitude($coordinates['longitude']);
            $manager->persist($event);
        }

        $manager->flush();
    }
}

