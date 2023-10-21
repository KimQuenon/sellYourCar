<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarType;
use App\Repository\CarRepository;
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
    public function create(): Response
    {
        $car = new Car();
        $form = $this->createform(CarType::class, $car);

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
