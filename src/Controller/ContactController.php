<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * Page de contact
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
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

        return $this->render('contact/contact.html.twig', [
            'myForm' => $form->createView()
        ]);
    }


    /**
     * Afficher la messagerie
     *
     * @param ContactRepository $messagerepo
     * @return Response
     */
    #[Route('/messages', name: 'messagerie')]
    #[IsGranted('ROLE_ADMIN')]
    public function message(ContactRepository $messagerepo): Response
    {
        $messages = $messagerepo->findAll();

        return $this->render('contact/messagerie.html.twig', [
            'messages' => $messages,
        ]);

    }


    /**
     * Supprimer un message
     *
     * @param Contact $message
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("messages/{id}/delete", name:"message_delete")]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteMessage(Contact $message, EntityManagerInterface $manager): Response
    {
            $manager->remove($message);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le message de <strong>".$message->getName()."</strong> a bien été supprimé!"
            );

        return $this->redirectToRoute('messagerie');

        return $this->render('contact/delete.html.twig', [
            
        ]);
    }

}
