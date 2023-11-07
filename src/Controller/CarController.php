<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarType;
use App\Entity\Images;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CarController extends AbstractController
{
    /**
     * Afficher toutes les voitures
     *
     * @param CarRepository $repo
     * @return Response
     */
    #[Route('/cars', name: 'cars_index')]
    public function index(CarRepository $repo): Response
    {
        $cars = $repo->findAll();

        return $this->render('cars/index.html.twig', [
            'cars' => $cars,
        ]);
    }

    /**
     * Ajout d'une voiture
     *
     * @return Response
     */
    #[Route("/cars/new", name:"cars_create")]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $car = new Car();

        
        $form = $this->createform(CarType::class, $car);

        //traitement des données - associations aux champs respectifs - validation
        $form->handleRequest($request);

        //form complet et valid -> envoi bdd + message et redirection
        if($form->isSubmitted() && $form->IsValid())
        {
            //gestion des images
            foreach($car->getImages() as $image)
            {
                $image->setCar($car);
                $manager->persist($image);
            }

            $car->setAuthor($this->getUser());

            $manager->persist($car);
            $manager->flush();

            $this->addFlash(
                'success',
                "La fiche de <strong>".$car->getBrand()." ".$car->getModel()."</strong> a bien été enregistrée."
            );

            return $this->redirectToRoute('cars_show', [
                'slug'=> $car->getSlug()
            ]);
        }

        return $this->render("cars/new.html.twig",[
            'myForm' => $form->createView()
        ]);

    }


    /**
     * Éditer une voiture
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Car $car
     * @return Response
     */
    #[Route("cars/{slug}/edit", name:"cars_edit")]
    #[IsGranted(
        attribute: new Expression('(user === subject and is_granted("ROLE_USER")) or is_granted("ROLE_ADMIN")'),
        subject: new Expression('args["car"].getAuthor()'),
        message: "Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier"
    )]
    public function edit(Request $request, EntityManagerInterface $manager, Car $car): Response
    {
        
            $form = $this->createForm(CarType::class, $car); //récupérer le formulaire
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {

                // gestion des images 
                foreach($car->getImages() as $image)
                {
                    $image->setCar($car);
                    $manager->persist($image);
                }

                $manager->persist($car);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "L'annonce <strong>".$car->getBrand()." ".$car->getModel()."</strong> a bien été modifiée!"
                );

                return $this->redirectToRoute('cars_show',[
                    'slug' => $car->getSlug()
                ]);

            }
        
        return $this->render("cars/edit.html.twig",[
            "car"=> $car,
            "myForm"=> $form->createView()
        ]);
    }

    /**
     * Supprimer une voiture
     *
     * @param Car $car
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("cars/{slug}/delete", name:"cars_delete")]
    #[IsGranted(
        attribute: new Expression('(user === subject and is_granted("ROLE_USER")) or is_granted("ROLE_ADMIN")'),
        subject: new Expression('args["car"].getAuthor()'),
        message: "Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier"
    )]
    public function deleteCars(Car $car, EntityManagerInterface $manager): Response
    {
            $manager->remove($car);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>".$car->getBrand()." ".$car->getModel()."</strong> a bien été supprimée!"
            );

        return $this->redirectToRoute('cars_index');

        return $this->render('cars/delete.html.twig', [
            
        ]);
    }

    /**
     * Afficher chaque voiture
     *
     * @param string $slug
     * @param Car $car
     * @return Response
     */
    #[Route("/cars/{slug}", name:"cars_show")]
    public function show(string $slug, Car $car, CarRepository $repo): Response
    {
        //recup l'auteur de l'annonce
        $owner = $car->getAuthor();
        //recup les autres voitures de ce même auteur et les mettre dans un tab
        $otherCars = $owner->getCars()->toArray(); 
        //évite que la voiture de l'annonce apparaisse dans les suggestions
        $otherCars = array_filter($otherCars, function ($otherCar) use ($car) {
            return $otherCar !== $car;
        });
        
        return $this->render("cars/show.html.twig",[
            'car'=>$car,
            'owner'=>$owner,
            'otherCars' => $otherCars,
        ]);
    }
}
