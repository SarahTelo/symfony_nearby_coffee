<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

//TODO : créer un service qui contrôlera si le rôle de l'utilisateur est Admin ou Responsible => afin de l'appeler pour l'édition d'un utilisateur

//TODO : mettre les token csrf dans les routes POST
//TODO : mettre un autre token dans les autres routes

//TODO : comprendre la redirection automatique

/**
 * *Classe de gestion des utilisateurs
 * 
 * @Route("/admin/user", name="user")
 */
class UserController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/list", name="_list")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * *Ajout d'un utilisateur
     * 
     * @Route("/new", name="_new")
     * 
     * @param request
     * @return void
     */
    public function userNew(Request $request): Response
    {
        //création du moule'utilisateur"
        $user = new User();
        //création du moule "formulaire" de type "Coffee"
        $form = $this->createForm(UserType::class, $user, [ 'attr' => ['novalidate' => 'novalidate'] ]);
        //stockage des données du formulaire dans la request
        $form->handleRequest($request);
        
        //-> si le formulaire a été validé, récupération des données et traitement de celles-ci
        if ($form->isSubmitted() && $form->isValid()) 
        {
            //les données du formulaire sont rentrées dans les propriétés de $user
            $user = $form->getData();

            //date de création
            $user->setCreatedAt( new \DateTime('now') );
            //stockage du mdp transmis par le formulaire
            $originalPassword = $user->getPassword();
            //encodage du mdp
            $user->setPassword($this->passwordEncoder->encodePassword($user, $originalPassword));

            //stockage du nom de l'utilisateur pour le réutiliser
            $userFullName = $user->getFirstname() . " " . $user->getLastname();

            try {
                //appel de l'entity manager
                $em = $this->getDoctrine()->getManager();
                //sauvegarde
                $em->persist($user);
                //envoi à la BDD
                //! à décommenter pour sauvegarder en BDD
                $em->flush();

                //remplissage des variables pour le message d'information d'état final
                $result = 'success';
                $message = "L'utilisateur {$userFullName} a bien été ajouté";
                //$route = 'user_list';
            } catch (\Throwable $th) {
                //remplissage des variables pour le message d'information d'état final
                $result = 'danger';
                $message = "L'utilisateur {$userFullName} n'a pas pu être ajouté, veuillez contacter l'administrateur du site.";
                //$route = 'user_new';
            }

            //remplissage du message d'information
            $this->addFlash($result, $message);
            //redirection vers la route choisie
            return $this->redirectToRoute('main_home');
        }
        //-> sinon affichage du formulaire vide
        else
        {
            return $this->render('user/new.html.twig', [ 'form_user' => $form->createView() ] ); 
        }
    }
}
