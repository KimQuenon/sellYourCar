<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
    {
    /**
     * système de connexion
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route('/login', name: 'account_login')]
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError(); //récupérer la dernière erreur engendrée
        $username = $utils->getLastUsername(); //récupérer le dernier username à s'être connecté

        return $this->render('account/index.html.twig', [
            'hasError' => $error !== null, //pas nul => afficher erreur
            'username'=> $username
        ]);
    }

    /**
     * système de déconnexion
     *
     * @return Response
     */
    #[Route('/logout', name: 'account_logout')]
    public function logout(): Void
    {

    }
}
