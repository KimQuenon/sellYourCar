<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;

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
        $loginError = null; //init la var pour toomanyattempts

        if($error instanceof TooManyLoginAttemptsAuthenticationException)
        {
            //l'erreur est due à la limitation de tentative de connexion
            $loginError= "Trop de tentatives de connexion, réessayez plus tard...";

        }

        return $this->render('account/index.html.twig', [
            'hasError' => $error !== null, //pas nul => afficher erreur
            'username'=> $username,
            'loginError'=> $loginError
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
