<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contactpage')]
    public function contact(Request $request, EntityManagerInterface $manager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $manager->persist($contact);
            $manager->flush();

            $form = $this->createForm(ContactType::class, new Contact());//init d'un nouveau form

            $this->addFlash(
                'success',
                'Votre message nous est bien parvenu, nous ne manquerons pas de vous recontacter dans les plus bref délais.'    
            );
        }

        return $this->render('contact.html.twig', [
            'myForm' => $form->createView()
        ]);
    }


    #[Route('/messages', name: 'messagerie')]
    public function message(ContactRepository $messagerepo): Response
    {
        $messages = $messagerepo->findAll();

        return $this->render('messages.html.twig', [
            'messages' => $messages,
        ]);

    }

}
