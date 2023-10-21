<?php

namespace App\Controller;

use App\Entity\Car;
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
