<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
//use App\Service\ContentRename as ServiceContentRename;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * *Affichage de la liste des utilisateurs avec les détails
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

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * *Affichage du détail d'un utilisateur
     * 
     * @Route("/{id}/detail", name="_detail", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @param id $id
     * @return void
     */
    public function userDetail(int $id): Response
    {
        //récupération de l'utilisateur actuel
        $currentUserId = $this->getUser()->getId();
        //vérification de ses rôles
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        //s'il n'est pas admin, alors l'id sera le sien
        if ($hasAccess !== true) {
            $id = $currentUserId;
        }   

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
        ]);
    }

    /**
     * *Ajout d'un utilisateur
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
                $route = 'user_list';
                $userId = $user->getId();
            } catch (\Throwable $th) {
                //remplissage des variables pour le message d'information d'état final
                $result = 'danger';
                $message = "L'utilisateur {$userFullName} n'a pas pu être ajouté, veuillez contacter l'administrateur du site.";
                $route = 'user_new';
                $userId = null;
            }

            //remplissage du message d'information
            $this->addFlash($result, $message);
            //redirection vers la route choisie
            return $this->redirectToRoute($route, ['userId' => $userId]);
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
     * @Route("/{id}/edit", name="_edit", methods={"GET", "PUT", "PATCH", "POST"}, requirements={"id"="\d+"})
     * 
     * @param int $id
     * @param request $request
     * @return void
     */
    public function userEdit(Request $request, int $id): Response
    {
        //reconnexion obligatoire si connexion précédente étaient en IS_AUTHENTICATED_REMEMBERED
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //méthode POST utilisée (plus rapide)
        /** @var UserRepository $repository */
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);
        //les données du utilisateur à éditer sont injecté dans le "formulaire" créé
        $form = $this->createForm(UserType::class, $user, [ 'attr' => ['novalidate' => 'novalidate'] ]);
        //stockage de l'ancien nom
        $oldName = $user->getFirstname() . " " . $user->getLastname();
        //stockage des données du formulaire dans la request
        $form->handleRequest($request);

        //dd($form->getData());
        //dump($request);
        //dd($form->handleRequest($request));
        
        //-> si le formulaire a été validé, récupération des données et traitement de celles-ci
        if ($form->isSubmitted() && $form->isValid()) 
        {
            dd("ok");
            //dd($form->getData());
            //date de mise à jour
            $user->setUpdatedAt( new \DateTime('now') );
            //stockage du nom de l'utilisateur pour le réutiliser
            $userFullName = $user->getFirstname() . " " . $user->getLastname();

            //$arrayRoles = $user->getRoles();
            //$contentRename = new ContentRename;
            //$arrayRolesModify = $contentRename->renamedRoles($arrayRoles);
            //$userRoles = implode(", ", $arrayRolesModify);
            //$user->setStatus($userRoles);

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
            return $this->redirectToRoute('user_detail', [ 'id' => $user->getId() ]);
        }
        //-> sinon affichage du formulaire avec les données du utilisateur à éditer
        else
        {
            return $this->render('user/edit.html.twig', [ 
                'form_user_edit' => $form->createView(), 
                'name' => $user->getFirstname() . " " . $user->getLastname(), 
                'userDetailId' => $user->getId() ]); 
        }
    }
    /**
     * *Edition du mot de passe
     * 
     * @Route("/{id}/edit/password", name="_edit_password", methods={"GET", "PUT", "PATCH", "POST"}, requirements={"id"="\d+"})
     * 
     * @param int $id
     * @param request $request
     * @return void
     */
    public function userEditPassword(Request $request, int $id): Response
    {
        //TODO : à faire
        return $this->render('user/editPassword.html.twig', [] );

    }

    /**
     * *Suppression d'un utilisateur
     * 
     * @Route("/{id}/delete", name="_delete", methods={"GET", "DELETE"}, requirements={"id"="\d+"})
     * 
     * @param id $id
     * @return void
     */
    public function userDelete(int $id): Response
    {
        //reconnexion obligatoire si connexion précédente étaient en IS_AUTHENTICATED_REMEMBERED
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var UserRepository $repository */
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);
        //stockage du nom de l'utilisateur pour le réutiliser
        $userFullName = $user->getFirstname() . " " . $user->getLastname();

        try {
            //appel de l'entity manager
            $em = $this->getDoctrine()->getManager();
            //sauvegarde
            $em->remove($user);
            //envoi à la BDD
            //! à décommenter pour sauvegarder en BDD => 
            //!$em->flush();
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
