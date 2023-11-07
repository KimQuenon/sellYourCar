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
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Fakecar($faker)); //appel au faker de voiture
        $slugify = new Slugify(); //init d'un nouveau slug hors boucle! sinon bouclé x fois et perte d'optimisation
        
        //création d'un admin
        $admin = new User();
        $admin->setFirstName('Kim')
            ->setLastName('Quenon')
            ->setEmail('quenonk@epse.be')
            ->setPassword($this->passwordHasher->hashPassword($admin, 'password'))
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
            ->setRoles(['ROLE_ADMIN'])
            ->setPicture('');

        $manager->persist($admin);

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
                ->setPicture('');

                $manager->persist($user);

                $users[] = $user; //ajouter un user au tableau pour les annonces


        }

        /**
         * boucler 30 objets gérés par faker pour les voitures
         */

        $carCover =[
            'https://static-assets.tesla.com/configurator/compositor?&bkba_opt=1&view=STUD_3QTR&size=1400&model=ms&options=$BP02,$ADPX2,$GLTL,$AU01,$APF1,$APH4,$APPB,$X028,$BTX5,$BS00,$BCMB,$CH04,$CF00,$CW02,$COFR,$X039,$IDCF,$X027,$DRLH,$DU00,$AF02,$FMP6,$FG02,$DCF0,$FR04,$TD00,$X007,$X011,$PI01,$IX00,$X001,$LP01,$LT5P,$MI03,$X037,$MDLS,$DV4W,$X025,$X003,$ZINV,$PPMR,$PS01,$PK00,$X031,$PX00,$PF00,$X043,$TM00,$BR04,$REEU,$RFP2,$EUSB,$X014,$S32P,$ME02,$QTFP,$SR07,$SP01,$X021,$SC04,$SU01,$TR00,$TIM3,$DSHG,$MT75A,$UTSB,$WTAS,$WR02,$YFCC,$CPF1&crop=1400,850,300,130&',
            'https://www.seat.fr/content/dam/countries/fr/seat-website/car-shopping-tools/ventes-privees-copa/ibiza-copa.png',
            'https://www.leaseplan.com/-/media/leaseplan-digital/int/blog/2022/best-cars-of-2023/kia-ev6.webp?rev=-1&mw=850',
            'https://www.club-auto.com/cdn-ad/cdn-autodiscount/storage/cars/34804/metallisee-gris-artense-std.png',
            'https://pim.suzuki.be/image/trimLevel/1500/1000/contain/webp/7236_ZWG_LV_Swift_1.4_Sport_Speedy_Blue.png',
            'https://www.fastlease.fr/wp-content/uploads/2023/07/slider_volvo-ex30.png',
            'https://pim.suzuki.be/image/trimLevel/1500/1000/contain/webp/6815_C7R_LV_Swift_1.2_Comfort_Smart_Hybrid_Speedy_Blue_Super_Black.png',
            'https://uploads-ssl.webflow.com/6218c57f9043f673420ef4d9/62597bd2d654fcbdb8c196cf_Lamborghini%20urus%20jaune.png',
            'https://www.free2move.com/api/media/20220504/dxJBl6mUCV1zrJJi1jpiWxJ1qgFWSGY8j7eyGQK3bHVm2TfnqLUskKYBUFnLUCHL369-M4_meFaWQyM26JebIdRbNedPsZ4ziIPbqi_Nld-uX8R89dnbDMJ3Qet_eY_l/cod-webapp-opel-corsa-01.png',
            'https://assets.meinauto.de/image/upload/f_auto/q_auto:eco/c_scale,w_auto/v1//prod/opel/astra/5/5wagon-innovation/opel_16astrastinnovationes5b_angularfront.png',
            'https://www.recharge.gr/wp-content/uploads/mokka-e.png',
            'https://cdn11.bigcommerce.com/s-f6pct9vuuz/images/stencil/1280x1280/products/494/3166/Kids_Licensed_Classic_Pink_Volkswagen_Beetle_12v_Ride_On_Car__72673.1670585777.png?c=1',
            'https://uploads-ssl.webflow.com/64245dbde52a86602e89015d/64245dbde52a86319b89036a_Lamborghini2.png',
            'https://toyworldmag.co.uk/wp-content/uploads/2019/12/VW-Beetle-scaled.png',
            'https://www.arnoldclark.com/cdn/images/shared/ac-brand/vehicles/vauxhall-mokka-silver.png',
            'https://images.carexpert.com.au/resize/3000/-/app/uploads/2022/08/2023-MG-MG4-Mulan-dual-motor-1.png',
            'https://www.motortrend.com/uploads/sites/10/2023/03/2023-toyota-highlander-hybrid-limited-4wd-suv-angular-front.png',
            'https://static.tcimg.net/vehicles/primary/1201922b01cd20b0/2023-Mercedes-Benz-SL-gray-full_color-driver_side_front_quarter.png',
            'https://microlino-car.com/resources/public/microlino/col-red_pac-pre_rim-rdp_rof-sun_int-dip@1.png',
            'https://i2.wp.com/x3adventures.com/wp-content/uploads/2022/11/Huracan-cut.png?fit=1024%2C768&ssl=1',
            'https://www.seat.com/content/dam/public/seat-website/myco/2425/carworlds/leon/overview/version-view/leon-style/seat-leon-5d-midnight-black-style-trim-colour-alloy-wheels.png',
            'https://images.ctfassets.net/6x2h5ns7uwip/3AmOhjXySrI1EByu0lUXEr/aa251c3ff45a22ce960e4ced175c9c45/CA0250_FEATURED_CARS_MODULE_CUTOUTS_CORSA_V2_REVERSED_CA0250_FEATURED_CARS_MODULE_CUTOUTS_F4F7F5.png?f=center&fit=fill&fm=webp&h=676&w=1200',
            'https://mclaren.scene7.com/is/image/mclaren/mclaren_automotive_600lt_spider_front_side:crop-2x1?fmt=png-alpha&wid=1940&hei=970',
            'https://cdn.rebrickable.com/media/thumbs/sets/60398-1/122724.jpg/1000x800p.jpg?1684420838.1422749',
            'https://pngimg.com/uploads/volkswagen/volkswagen_PNG1787.png',
            'https://cdn.pixabay.com/photo/2020/06/11/17/02/show-car-5287477_1280.png',
            'https://lzd-img-global.slatic.net/g/p/55a8448d307aed4f177450e2a0f5c9b5.png_960x960q80.png_.webp',
            'https://cool-toys.com.au/wp-content/uploads/2018/07/BCJ81_PoP_13_1_w900_1390060415.png',
            ];
        
        for ($i=1; $i <= 30 ; $i++) { 
            $car = new Car();

            $car->setModel($faker->vehicleModel)
                ->setBrand($faker->vehicleBrand)
                ->setCoverImage($carCover[array_rand($carCover)])
                ->setKm(rand(0,80000))
                ->setPrice(rand(15000,50000))
                ->setOwners(rand(1,3))
                ->setCylinder(rand(1,12))
                ->setPower(rand(80,500))
                ->setCarburant($faker->vehicleFuelType)
                ->setYear($faker->biasedNumberBetween(1990, date('Y'), 'sqrt'))
                ->setTransmission($faker->vehicleGearBoxType)
                ->setContent('<p>'.join('</p><p>', $faker->paragraphs(2)).'</p>')
                ->setOptions('<p>'.join('</p><p>', $faker->paragraphs(2)).'</p>')
                ->setAuthor($users[rand(0, count($users)-1)]); //rand pour un id d'author

            //gestion de la galerie associée
            $carImages = [
                'https://i.pinimg.com/originals/af/6c/f0/af6cf01468224408ece755b199fb9776.jpg',
                'https://www.usatoday.com/gcdn/presto/2019/03/06/USAT/dbee093f-65b9-4cd1-866e-524761d66986-Large-35791-HyundaiMotorSharesFirstGlimpseofAll-NewSonata.jpg',
                'https://cfx-wp-images.imgix.net/2022/10/Registration-in-Car-min.jpg?auto=compress%2Cformat&ixlib=php-3.3.0&s=a84b40ab413661835c74953342756c92',
                'https://lexanimotorcars.com/img/home-hero-yukon-int.jpg?w=2000&h=1000&fit=crop&fm=pjpg',
                'https://images.cars.com/cldstatic/wp-content/uploads/toyota-c-hr-2020-ceiling--front-row--interior-29.jpg',
                'https://www.autozone.com/cdn/images/B2C/US/media/Landing/RAInteriorAccess/ia-lp-personalize-header-d.jpg',
                'https://repairsmith-prod-wordpress.s3.amazonaws.com/2022/11/holding-steering-wheel.jpg',
                'https://d2hucwwplm5rxi.cloudfront.net/wp-content/uploads/2021/08/26123338/How-steering-wheel-returns-to-center-Cover-20210826.jpg',
            ];

            for ($g=1; $g <= rand(2,5); $g++)
            { 
                $image = new Images();
                $image->setUrl($carImages[array_rand($carImages)])
                    ->setCaption($faker->sentence())
                    ->setCar($car);
                $manager->persist($image);
            }
            
            $manager->persist($car);
        }

        $manager->flush();
    }
}
