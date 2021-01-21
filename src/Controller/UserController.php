<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserTypeEdit;
use App\Form\UserTypePassword;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use SluggerService;
use App\Service\ContentRename;

//TODO : mettre un autre token dans les autres routes
//TODO : comprendre la redirection automatique

/**
 * *Classe de gestion des utilisateurs (IsGranted)
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
     * *Affichage de la liste des utilisateurs avec les détails (roles: admin et super admin)
     * 
     * @Route("/list", name="_list", methods={"GET"})
     * 
     * @return void
     */
    public function userList(): Response
    {
        /** @var UserRepository $repository */
        //appel du repository
        $repository = $this->getDoctrine()->getRepository(User::class);
        //appel de la fonction présente dans le repository
        $users = $repository->findAll();

        return $this->render('user/list.html.twig', [ 'users' => $users ]);
    }

    /**
     * *Affichage du détail d'un utilisateur
     * 
     * @Route("/detail/{slug}", name="_detail", methods={"GET"})
     * 
     * @param user $user => injection de dépendance
     * @return void
     */
    public function userDetail(User $user): Response
    {
        //récupération de l'utilisateur actuel
        $currentUserId = $this->getUser()->getId();
        //récupération de l'id de l'utilisateur ciblé par son slug
        $id = $user->getId();
        //vérification de ses rôles
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        //s'il n'est pas admin, alors l'id sera le sien, sinon, recherche de l'utilisateur ciblé par son slug
        $id = $hasAccess ? $id : $currentUserId;

        /** @var UserRepository $repository */
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);

        //réécriture des rôles
        $arrayRoles = $user->getRoles();
        $contentRename = new ContentRename;
        $arrayRolesModify = $contentRename->renamedRoles($arrayRoles);

        return $this->render('user/detail.html.twig', [
            'user' => $user,
            'roles' => $arrayRolesModify,
            'userSlug' => $user->getSlug(),
        ]);
    }

    /**
     * *Ajout d'un utilisateur (roles: admin et super admin)
     * 
     * @Route("/new", name="_new", methods={"POST", "GET"})
     * 
     * @param request $request
     * @return void
     */
    public function userNew(Request $request): Response
    {
        //reconnexion obligatoire si connexion précédente étaient en IS_AUTHENTICATED_REMEMBERED
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //création du moule "utilisateur"
        $user = new User();
        //création du moule "formulaire" de type "User"
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
            //todo : prochainement: mettre un mot de passe automatique qui devra être obligatoirement changé lors de la première connexion
            //stockage du mdp transmis par le formulaire
            $originalPassword = $user->getPassword();
            //encodage du mdp
            $user->setPassword($this->passwordEncoder->encodePassword($user, $originalPassword));

            //stockage du nom de l'utilisateur pour le réutiliser
            $userFullName = $user->getFirstname() . " " . $user->getLastname();
            //instancier le service
            $slugger = new SluggerService();
            //appel de la fonction du service + ajout d'un identifiant unique (basé sur la date et l'heure)
            $userSlug = $slugger->slugify($userFullName). "-" .uniqid();
            //sauvegarde du nom en format slug
            $user->setSlug($userSlug);

            try {
                //appel de l'entity manager
                $em = $this->getDoctrine()->getManager();
                //sauvegarde et envoi à la BDD
                $em->persist($user);
                //envoi à la BDD
                $em->flush();
                //remplissage des variables pour le message d'information d'état final
                $result = 'success';
                $message = "L'utilisateur {$userFullName} a bien été ajouté";
                $route = 'user_detail';
                $userSlug = $user->getSlug();
            } catch (\Throwable $th) {
                //remplissage des variables pour le message d'information d'état final
                $result = 'danger';
                $message = "L'utilisateur {$userFullName} n'a pas pu être ajouté, veuillez contacter l'administrateur du site.";
                $route = 'user_list';
                $userSlug = null;
            }

            //remplissage du message d'information
            $this->addFlash($result, $message);
            //redirection vers la route choisie
            return $this->redirectToRoute($route, [ 'slug' => $userSlug ]);
        }
        //-> sinon affichage du formulaire vide
        else
        {
            return $this->render('user/new.html.twig', [ 'form_user' => $form->createView() ] ); 
        }
    }

    /**
     * *Edition du profil d'un utilisateur
     * 
     * @Route("/edit/{slug}", name="_edit", methods={"GET", "PUT", "PATCH", "POST"})
     * 
     * @param request $request
     * @param user $user => injection de dépendance
     * @return void
     */
    public function userEdit(Request $request, User $user): Response
    {
        //méthode POST utilisée (plus rapide)
        //reconnexion obligatoire si connexion précédente étaient en IS_AUTHENTICATED_REMEMBERED
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //les données de l'utilisateur à éditer sont injectées dans le "formulaire" créé
        $form = $this->createForm(UserTypeEdit::class, $user, [ 'attr' => [
            'novalidate' => 'novalidate', 
            'data' => $user->getId(),
        ]]);
        //stockage de l'ancien nom
        $oldName = $user->getFirstname() . " " . $user->getLastname();
        //stockage des données du formulaire dans la request
        $form->handleRequest($request);
         
        //-> si le formulaire a été validé, récupération des données et traitement de celles-ci
        if ($form->isSubmitted() && $form->isValid()) 
        {
            //date de mise à jour
            $user->setUpdatedAt( new \DateTime('now') );

            //stockage du nom de l'utilisateur pour le réutiliser
            $userFullName = $user->getFirstname() . " " . $user->getLastname();
            //instancier le service
            $slugger = new SluggerService();
            //appel de la fonction du service + ajout d'un identifiant unique (basé sur la date et l'heure)
            $userSlug = $slugger->slugify($userFullName). "-" .uniqid();
            //sauvegarde du nom en format slug
            $user->setSlug($userSlug);

            try {
                //appel de l'entity manager
                $em = $this->getDoctrine()->getManager();
                //envoi à la BDD
                $em->flush();
                //remplissage des variables pour le message d'information d'état final
                $result = 'success';
                $message = "Le utilisateur {$oldName} a bien été modifié";
            } catch (\Throwable $th) {
                //remplissage des variables pour le message d'information d'état final
                $result = 'danger';
                $message = "Le utilisateur {$oldName} n'a pas pu être modifié, veuillez contacter l'administrateur du site.";
            }

            //remplissage du message d'information
            $this->addFlash($result, $message);
            //redirection vers la route choisie
            return $this->redirectToRoute('user_detail', [ 'slug' => $user->getSlug() ]);
        }
        //-> sinon affichage du formulaire avec les données du utilisateur à éditer
        else
        {
            return $this->render('user/edit.html.twig', [ 
                'form_user_edit' => $form->createView(), 
                'name' => $user->getFirstname() . " " . $user->getLastname(), 
                'userSlug' => $user->getSlug(),
            ]); 
        }
    }

    /**
     * *Edition du mot de passe
     * 
     * @Route("/edit/password/{slug}", name="_edit_password", methods={"GET", "PUT", "PATCH", "POST"})
     * 
     * @param user $user
     * @param request $request
     * @return void
     */
    public function userEditPassword(Request $request, User $user): Response
    {
        //reconnexion obligatoire si connexion précédente étaient en IS_AUTHENTICATED_REMEMBERED
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        //les données de l'utilisateur à éditer sont injectées dans le "formulaire" créé
        $form = $this->createForm(UserTypePassword::class, $user, [ 'attr' => ['novalidate' => 'novalidate'] ]);
        //stockage des données du formulaire dans la request
        $form->handleRequest($request);
        
        //stockage du nom
        $userFullName = $user->getFirstname() . " " . $user->getLastname();

        if ($form->isSubmitted() && $form->isValid()) 
        {
            //date de création
            $user->setCreatedAt( new \DateTime('now') );
            //stockage du mdp transmis par le formulaire
            $originalPassword = $user->getPassword();
            //encodage du mdp
            $user->setPassword($this->passwordEncoder->encodePassword($user, $originalPassword));

            try {
                //appel de l'entity manager
                $em = $this->getDoctrine()->getManager();
                //sauvegarde
                //$em->persist($user);
                //envoi à la BDD
                $em->flush();
                //remplissage des variables pour le message d'information d'état final
                $result = 'success';
                $message = "Votre mot de passe a bien été modifié.";
            } catch (\Throwable $th) {
                //remplissage des variables pour le message d'information d'état final
                $result = 'danger';
                $message = "Votre mot de passe n'a pas pu être modifié, veuillez contacter l'administrateur du site.";
            }
            
            //remplissage du message d'information
            $this->addFlash($result, $message);
            //redirection vers la route choisie
            return $this->redirectToRoute('user_detail', [ 'slug' => $user->getSlug() ]);
        }
        else
        {
            return $this->render('user/editPassword.html.twig', [
                'form_user_edit_password' => $form->createView(), 
                'name' => $userFullName, 
                'userSlug' => $user->getSlug(),
            ]);
        }
    }

    /**
     * *Suppression d'un utilisateur (role: super admin)
     * 
     * @Route("/delete/{slug}", name="_delete", methods={"GET", "DELETE"})
     * 
     * @param user $user
     * @return void
     */
    public function userDelete(User $user): Response
    {
        //reconnexion obligatoire si connexion précédente étaient en IS_AUTHENTICATED_REMEMBERED
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        //stockage du nom de l'utilisateur pour le réutiliser
        $userFullName = $user->getFirstname() . " " . $user->getLastname();

        try {
            //appel de l'entity manager
            $em = $this->getDoctrine()->getManager();
            //sauvegarde
            $em->remove($user);
            //envoi à la BDD
            $em->flush();
            //remplissage des variables pour le message d'information d'état final
            $result = 'success';
            $message = "L'utilisateur {$userFullName} a bien été supprimé";
        } catch (\Throwable $th) {
            //remplissage des variables pour le message d'information d'état final
            $result = 'danger';
            $message = "L'utilisateur {$userFullName} n'a pas pu être supprimé, veuillez contacter l'administrateur du site.";
        }

        //remplissage du message d'information
        $this->addFlash($result, $message);
        //redirection vers la route choisie
        return $this->redirectToRoute('user_list');
    }
}
