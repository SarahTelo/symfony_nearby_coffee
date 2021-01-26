<?php

namespace App\Controller;

use App\Entity\Coffee;
use App\Repository\CoffeeRepository;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_home")
     */
    public function home(): Response
    {
        /** @var CoffeeRepository $repository */
        //appel du repository des cafés
        $repository = $this->getDoctrine()->getRepository(Coffee::class);
        //recherche du dernier café ajouté
        $lastCoffee = $repository->findLastCoffee();

        return $this->render('main/home.html.twig', [ 'lastCoffee' => $lastCoffee ]);
    }

    /**
     * *Page de contact
     * 
     * @Route("/contact", name="main_contact")
     * 
     * @param request $request
     * @param Swift_Mailer $mailer
     * @return void
     */
    public function contact(Request $request, \Swift_Mailer $mailer): Response
    {
        //création du moule "formulaire" de type "contact"
        $form = $this->createForm(ContactType::class);
        //stockage des données du formulaire dans la request
        $form->handleRequest($request);

        //-> si le formulaire a été validé, récupération des données et traitement de celles-ci
        if ($form->isSubmitted() && $form->isValid()) 
        {
            //récupération des données du formulaire
            $contact = $form->getData();
            $message = new \Swift_Message('Nouveau contact');
            //Expéditeur
            $message->setFrom($contact['email'])
            //Destinataire
            ->setTo('contact@sarah-dev.com');

            try {
                //envoie du message
                $mailer->send($message);
                //remplissage des variables pour le message d'information d'état final
                $result = 'success';
                $message = 'Votre message a été transmis, nous vous répondrons dans les meilleurs délais.';
            } catch (\Throwable $th) {
                //remplissage des variables pour le message d'information d'état final
                $result = 'danger';
                $message = 'Votre message n\'a pas été transmis, veuillez réessayer.';
            }

            //remplissage du message d'information
            $this->addFlash($result, $message);
            //redirection vers la route choisie
            return $this->redirectToRoute('main_home');
        }
        //-> sinon affichage du formulaire vide
        else
        {
            return $this->render('main/contact.html.twig', [ 'form_contact' => $form->createView() ]);
        }
    }

    /**
     * *Page à propos
     * 
     * @Route("/about", name="main_about")
     */
    public function about(): Response
    {
        return $this->render('main/about.html.twig');
    }
}
