<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\DeleteType;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    /**
     * Création du nouvel user
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route("/register", name:"account_register")]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid())
        {
            //gestion de l'image

            $file = $form['picture']->getData(); //recup données dans le form

            //si champs rempli
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); //recup nom du fichier sans l'extension
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename); //enlève les caractères spéciaux
                $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension(); //nom unique
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'), //déplacement du fichier
                        $newFilename
                    );
                }catch(FileException $e){
                    return $e->getMessage();
                }
                $user->setPicture($newFilename);
            }

            //gestion de l'inscription dans la bdd
            $hash = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();


            return $this->redirectToRoute('account_login');
        }

        return $this->render("account/registration.html.twig",[
            'myForm'=>$form->createView()
        ]);
    }

    /**
     * Modification d'user
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/account/edit", name:"account_edit")]
    public function edit(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser(); //recup l'user connecté

        $filename = $user->getPicture();
        if(!empty($filename)){
            $user->setPicture(
                new File($this->getParameter('uploads_directory').'/'.$user->getPicture())
            );
        }
        
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user->setSlug('')
            ->setPicture($filename);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
            'success',
            'Les données ont été enregistrées avec succès'    
            );
        }

        return $this->render("account/edit.html.twig",[
            'myForm'=>$form->createView()
        ]);
    }


    /**
     * Modification du password
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route("/account/password-update", name:"account_password")]
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher):Response
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //verif mdp bdd & mdp du form
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())) {
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez renseigné n'est pas votre mot de passe actuel"));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                
                // verif si new mdp == old mdp
                if ($newPassword === $passwordUpdate->getOldPassword()) {
                    $form->get('newPassword')->addError(new FormError("Le nouveau mot de passe ne peut être identique à l'ancien mot de passe"));
                } else {
                    $hash = $hasher->hashPassword($user, $newPassword);
        
                    $user->setPassword($hash);
                    $manager->persist($user);
                    $manager->flush();
        
                    $this->addFlash(
                        'success',
                        'Le nouveau mot de passe a été modifié avec succès'
                    );
        
                    return $this->redirectToRoute('homepage');
                }
            }
        }
        
        

        return $this->render("account/password.html.twig",[
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Supprimer l'user
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $hasher
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/account/delete", name: "account_delete")]
    public function deleteAccount(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(DeleteType::class);

        //si l'user n'est pas connecté, renvoi vers connexion
        if (!$user) {

            $this->addFlash(
                'danger',
                'Connectez-vous à votre compte avant de le supprimer.'
            );
            return $this->redirectToRoute('account_login');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $submittedEmail = $data['email'];
            $submittedPassword = $data['password'];

            //verif si email est dans bdd
            if ($user->getEmail() === $submittedEmail) {
                $isPasswordValid = $hasher->isPasswordValid($user, $submittedPassword);

                //verif mdp
                if ($isPasswordValid) {
                    //remove si tout est ok
                    $manager->remove($user);
                    $manager->flush();

                    $this->addFlash(
                        'success',
                        'Votre compte a été supprimé avec succès.'
                    );

                    return $this->redirectToRoute('homepage');
                }
            }

            $this->addFlash(
                'danger',
                'L\'adresse e-mail ou le mot de passe est incorrect.'
            );
        }

        return $this->render('account/delete.html.twig', [
            'myForm' => $form->createView()
        ]);
    }


    /**
     * Afficher les voitures de l'user
     *
     * @return Response
     */
    #[Route("/account/cars", name:"account_cars")]
    public function displayCars(): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        $cars = $user->getCars(); // Récupérer les voitures liées à l'utilisateur
    
        return $this->render('account/cars.html.twig', [
            'cars' => $cars,
        ]);
    }


    /**
     * Afficher le profil
     *
     * @param string $slug
     * @param User $user
     * @return Response
     */
    #[Route("/account/profile/{slug}", name:"account_profile")]
    public function profile(string $slug, User $user): Response
    {
        return $this->render("account/profile.html.twig",[
            'user'=>$user
        ]);
    }

}
