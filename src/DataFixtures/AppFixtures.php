<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Car;
use App\Entity\Images;
use Cocur\Slugify\Slugify;
use Faker\Provider\Fakecar;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class AppFixtures extends Fixture
{
    /**
     * fonction privée pour le mdp
     *
     * @var [type]
     */
    private $passwordHasher;

    /**
     * Hashage du mdp
     *
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Application du faker sur les voitures - galeries - user
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        //CHANGER NOM DES FAKERS
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Fakecar($faker)); //appel au faker de voiture
        $slugify = new Slugify(); //init d'un nouveau slug hors boucle! sinon bouclé x fois et perte d'optimisation
        
        //gestion des users
        $users = []; //init d'un tab pour recup des users pour les annonces

        for($u = 1; $u <= 10; $u++)
        {
            $user = new User();
            
            $hash = $this->passwordHasher->hashPassword($user, 'password');

            $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>'.join('<p></p>',$faker->paragraphs(3)).'</p>')
                ->setPassword($hash)
                ->setPicture('https://picsum.photos/seed/picsum/500/500');

                $manager->persist($user);

                $users[] = $user; //ajouter un user au tableau pour les annonces


        }

        /**
         * boucler 30 objets gérés par faker pour les voitures
         */
        for ($i=1; $i <= 30 ; $i++) { 
            $car = new Car();

            $car->setModel($faker->vehicleModel)
                ->setBrand($faker->vehicleBrand)
                ->setCoverImage('https://picsum.photos/1000/400')
                ->setKm(rand(0,80000))
                ->setPrice(rand(15000,50000))
                ->setOwners(rand(1,3))
                ->setCylinder(rand(1,12))
                ->setPower(rand(80,500))
                ->setCarburant($faker->vehicleFuelType)
                ->setYear($faker->biasedNumberBetween(1990, date('Y'), 'sqrt'))
                ->setTransmission($faker->vehicleGearBoxType)
                ->setContent('<p>'.join('</p><p>', $faker->paragraphs(2)).'</p>')
                ->setOptions('<p>'.join('</p><p>', $faker->paragraphs(5)).'</p>')
                ->setAuthor($users[rand(0, count($users)-1)]); //rand pour un id d'author

            //gestion de la galerie associée
            for ($g=1; $g <= rand(2,5); $g++)
            { 
                $image = new Images();
                $image->setUrl('https://picsum.photos/id/'.$g.'/900')
                    ->setCaption($faker->sentence())
                    ->setCar($car);
                $manager->persist($image);
            }
            
            $manager->persist($car);
        }

        $manager->flush();
    }
}
