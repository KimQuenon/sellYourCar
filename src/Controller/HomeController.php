<?php

namespace App\Controller;

use App\Repository\CarRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(UserRepository $repoUser, CarRepository $repo): Response
    {
        //nbre de voitures
        $totalCars = $repo->count([]);
        //nbre de users
        $totalUsers = $repoUser->count([]);

        //afficher 3 annonces récentes
        $recentCars = $repo->findBy([], ['id' => 'DESC'], 3);

        //afficher 2 plus gros vendeurs
        $queryBuilder = $repoUser->createQueryBuilder('u');
        $queryBuilder
        ->select('u.id as user_id, u.firstName, u.lastName, COUNT(c.id) as car_count')
        ->leftJoin('u.cars', 'c')
        ->groupBy('u.id')
        ->orderBy('car_count', 'DESC')
        ->setMaxResults(2);

        $query = $queryBuilder->getQuery();
        $result = $query->getResult();


        return $this->render('home.html.twig', [
            'total_users'=>$totalUsers,
            'total_cars' => $totalCars,
            'cars' => $recentCars,
            'top_owners' => $result,
        ]);
    }
}
