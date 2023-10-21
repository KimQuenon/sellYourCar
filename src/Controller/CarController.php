<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $car = new Car();
        $form = $this->createform(CarType::class, $car);

        //traitement des données - associations aux champs respectifs - validation
        $form->handleRequest($request);

        //form complet et valid -> envoi bdd + message et redirection
        if($form->isSubmitted() && $form->IsValid())
        {
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
     * Afficher chaque voiture
     *
     * @param string $slug
     * @param Car $car
     * @return Response
     */
    #[Route("/cars/{slug}", name:"cars_show")]
    public function show(string $slug, Car $car): Response
    {
        return $this->render("cars/show.html.twig",[
            'car'=>$car
        ]);
    }
}
