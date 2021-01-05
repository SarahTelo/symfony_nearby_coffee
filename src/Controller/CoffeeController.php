<?php

namespace App\Controller;

use App\Entity\Coffee;
use App\Form\CoffeeType;
use App\Repository\CoffeeRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use SluggerService;
//use DateTime;
//use Symfony\Component\Serializer\SerializerInterface;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * *Classe de gestion d'affichage des cafés
 * 
 * @Route("/coffee", name="coffee")
 */
class CoffeeController extends AbstractController
{
    /**
     * *Affichage de la liste des cafés avec les détails
     * 
     * @Route("/list", name="_list", methods={"GET"})
     * 
     * @return void
     */
    public function coffeeList(): Response
    {
        /** @var CoffeeRepository $repository */
        //appel du repository
        $repository = $this->getDoctrine()->getRepository(Coffee::class);
        //appel de la fonction présente dans le repository
        $coffees = $repository->findAllDetailsList();

        return $this->render('coffee/list.html.twig', [
            'coffees' => $coffees,
        ]);
    }

    /**
     * *Affichage du détail du café
     * 
     * @Route("/detail/{slug}", name="_detail", methods={"GET"})
     * 
     * @param coffee => (injection de dépendance)
     * @return void
     */
    public function coffeeDetail(Coffee $coffee): Response
    {
        //EN PASSANT PAR L'ID: voir UserController/userDetail
        //avec le slug, Doctrine fait: select*from coffee where slug='{slug}'
        return $this->render('coffee/detail.html.twig', [
            'coffee' => $coffee,
        ]);
    }

    /**
     * *Ajout d'un café
     * 
     * @Route("/admin/new", name="_new", methods={"GET", "POST"})
     * 
     * @param request $request
     * @return void
     */
    public function coffeeNew(Request $request): Response
    {
        //création du moule "café"
        $coffee = new Coffee();
        //création du moule "formulaire" de type "Coffee"
        $form = $this->createForm(CoffeeType::class, $coffee, [ 'attr' => ['novalidate' => 'novalidate'] ]);
        //stockage des données du formulaire dans la request
        $form->handleRequest($request);

        //-> si le formulaire a été validé, récupération des données et traitement de celles-ci
        if ($form->isSubmitted() && $form->isValid()) 
        {
            //les données du formulaire sont rentrées dans les propriétés de $coffee
            $coffee = $form->getData();
            //date de création
            $coffee->setCreatedAt( new \DateTime('now') );
            //stockage du nom du café pour le réutiliser
            $coffeeName = $coffee->getName();

            //instancier le service
            $slugger = new SluggerService();
            //appel de la fonction du service
            $coffeeSlug = $slugger->slugify($coffeeName);
            //sauvegarde du nom en format slug
            $coffee->setSlug($coffeeSlug);

            //*possible si création d'un constructeur dans CoffeeController et ajout de la propriété privée "$slugger"
            //*$coffee->setSlug($this->slugger->slugify($coffeeName));

            try {
                //appel de l'entity manager
                $em = $this->getDoctrine()->getManager();
                //sauvegarde
                $em->persist($coffee);
                //envoi à la BDD
                //! à décommenter pour sauvegarder en BDD
                $em->flush();
                //remplissage des variables pour le message d'information d'état final
                $result = 'success';
                $message = "Le café {$coffeeName} a bien été ajouté";
                $route = 'coffee_detail';
            } catch (\Throwable $th) {
                //remplissage des variables pour le message d'information d'état final
                $result = 'danger';
                $message = "Le café {$coffeeName} n'a pas pu être ajouté, veuillez contacter l'administrateur du site.";
                $route = 'coffee_new';
                $coffeeSlug = null;
            }

            //remplissage du message d'information
            $this->addFlash($result, $message);
            //redirection vers la route choisie
            return $this->redirectToRoute($route, ['slug' => $coffeeSlug]);
        }
        //-> sinon affichage du formulaire vide
        else
        {
            return $this->render('coffee/new.html.twig', [ 'form_coffee' => $form->createView() ] ); 
        }
    }

    /**
     * *Edition d'un café
     * 
     * @Route("/admin/edit/{slug}", name="_edit", methods={"GET", "PUT", "PATCH", "POST"})
     * 
     * @param request $request
     * @param coffee => (injection de dépendance)
     * @return void
     */
    public function coffeeEdit(Request $request, coffee $coffee): Response
    {
        //le café à supprimer a été trouvé par l'injection de dépendance: "coffee $coffee"
        //il n'est pas nécessaire d'appeler le repository
        //méthode POST utilisée (plus rapide)

        //les données du café à éditer sont injecté dans le "formulaire" créé
        $form = $this->createForm(CoffeeType::class, $coffee, [ 'attr' => ['novalidate' => 'novalidate'] ]);
        //stockage des données du formulaire dans la request
        $form->handleRequest($request);

        //-> si le formulaire a été validé, récupération des données et traitement de celles-ci
        if ($form->isSubmitted() && $form->isValid()) 
        {
            //date de mise à jour
            $coffee->setUpdatedAt( new \DateTime('now') );
            //stockage du nom du café pour le réutiliser
            $coffeeName = $coffee->getName();

            //instancier le service
            $slugger = new SluggerService();
            //appel de la fonction du service
            $coffeeSlug = $slugger->slugify($coffeeName);
            //sauvegarde du nom en format slug
            $coffee->setSlug($coffeeSlug);

            try {
                //appel de l'entity manager
                $em = $this->getDoctrine()->getManager();
                //envoi à la BDD
                $em->flush();
                //remplissage des variables pour le message d'information d'état final
                $result = 'success';
                $message = "Le café {$coffeeName} a bien été modifié";
            } catch (\Throwable $th) {
                //remplissage des variables pour le message d'information d'état final
                $result = 'danger';
                $message = "Le café {$coffeeName} n'a pas pu être modifié, veuillez contacter l'administrateur du site.";
            }

            //remplissage du message d'information
            $this->addFlash($result, $message);
            //redirection vers la route choisie
            return $this->redirectToRoute('coffee_detail', [ 'slug' => $coffee->getSlug() ]);
        }
        //-> sinon affichage du formulaire avec les données du café à éditer
        else
        {
            return $this->render('coffee/edit.html.twig', [ 
                'form_coffee_edit' => $form->createView(), 
                'name' => $coffee->getName() ]); 
        }
    }

    /**
     * *Suppression d'un café
     * 
     * @Route("/admin/delete/{slug}", name="_delete", methods={"GET", "DELETE"})
     * 
     * @param coffee => (injection de dépendance)
     * @return void
     */
    public function coffeeDelete(coffee $coffee): Response
    {
        //stockage du nom du café pour le réutiliser
        $coffeeName = $coffee->getName();

        try {
            //appel de l'entity manager
            $em = $this->getDoctrine()->getManager();
            //sauvegarde
            $em->remove($coffee);
            //envoi à la BDD
            //! à décommenter pour sauvegarder en BDD => 
            //!$em->flush();
            //remplissage des variables pour le message d'information d'état final
            $result = 'success';
            $message = "Le café {$coffeeName} a bien été supprimé";
        } catch (\Throwable $th) {
            //remplissage des variables pour le message d'information d'état final
            $result = 'danger';
            $message = "Le café {$coffeeName} n'a pas pu être supprimé, veuillez contacter l'administrateur du site.";
        }

        //remplissage du message d'information
        $this->addFlash($result, $message);
        //redirection vers la route choisie
        return $this->redirectToRoute('coffee_list');
    }

}
