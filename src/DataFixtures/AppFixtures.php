<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Car;
use Cocur\Slugify\Slugify;
use Faker\Provider\Fakecar;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Fakecar($faker)); //appel au faker de voiture
        $slugify = new Slugify(); //init d'un nouveau slug hors boucle! sinon bouclé x fois et perte d'optimisation

        /**
         * boucler 30 objets gérés par faker
         */
        for ($i=1; $i <= 30 ; $i++) { 
            $car = new Car();
            $model = $faker->vehicleModel;
            $brand = $faker->vehicleBrand;
            $coverImage = $faker->imageUrl(1000,400);
            $carburant = $faker->vehicleFuelType;
            $year = $faker->biasedNumberBetween(1990, date('Y'), 'sqrt');
            $transmission = $faker->vehicleGearBoxType;
            $content= '<p>'.join('</p><p>', $faker->paragraphs(2)).'</p>';
            $options= '<p>'.join('</p><p>', $faker->paragraphs(5)).'</p>';

            $car->setModel($model)
                ->setBrand($brand)
                ->setContent($content)
                ->setCoverImage($coverImage)
                ->setKm(rand(0,80000))
                ->setPrice(rand(15000,50000))
                ->setOwners(rand(1,3))
                ->setCylinder(rand(1,12))
                ->setPower(rand(80,500))
                ->setCarburant($carburant)
                ->setYear($year)
                ->setTransmission($transmission)
                ->setContent($content)
                ->setOptions($options);

            $manager->persist($car);
        }

        $manager->flush();
    }
}
