<?php

namespace App\Controller;

use App\Entity\Coffee;
//use App\Entity\Roasting;
use App\Form\CoffeeType;
use App\Repository\CoffeeRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
//use Symfony\Component\Serializer\SerializerInterface;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * *Classe de gestion d'affichage des cafés servis
 * @Route("/coffee", name="coffee")
 */
class CoffeeController extends AbstractController
{
    /**
     * *Affichage de la liste des cafés avec les détails
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
     * TODO : mettre le slug du nom du café pour la route
     * @Route("/{id}/detail", name="_detail", methods={"GET"})
     * 
     * @return void
     */
    public function coffeeDetail($id): Response
    {
        /** @var CoffeeRepository $repository */
        //appel du repository
        $repository = $this->getDoctrine()->getRepository(Coffee::class);
        //appel de la fonction présente dans le repository
        $coffee = $repository->find($id);

        return $this->render('coffee/detail.html.twig', [
            'coffee' => $coffee,
        ]);
    }

    /**
     * *Ajout d'un café
     * @Route("/add", name="_add", methods={"GET", "POST"})
     * 
     * @param request
     * @return void
     */
    public function coffeeAdd(Request $request): Response
    {
        //création du moule "café"
        $coffee = new Coffee();
        //création du moule "formulaire" de type "Coffee"
        $form = $this->createForm(CoffeeType::class, $coffee, [ 'attr' => ['novalidate' => 'novalidate'] ]);

        //stockage des données du formulaire dans la request
        $form->handleRequest($request);

        //TODO : faire le validator dans l'entity "coffee"

        //si le formulaire a été validé, alors on récupère les données et on le traite
        if ($form->isSubmitted() && $form->isValid()) 
        {
            //les données du formulaire sont rentrées dans les propriétés de $coffee
            $coffee = $form->getData();
            //stockage du nom du café pour le réutiliser
            $coffeeName = $coffee->getName();

            try {
                //appel de l'entity manager
                $em = $this->getDoctrine()->getManager();
                //sauvegarde
                $em->persist($coffee);
                //envoie à la BDD
                //!à décommenter pour sauvegarder en BDD! => $em->flush();

                //remplissage des variables pour le message d'information d'état final
                $result = 'success';
                $message = "Le café {$coffeeName} a bien été ajouté";
                $route = 'coffee_list';
            } catch (\Throwable $th) {
                //remplissage des variables pour le message d'information d'état final
                $result = 'error';
                $message = "Le café {$coffeeName} n'a pas pu être ajouté, veuillez contacter l'administrateur du site.";
                $route = 'coffee_add';
            }

            //remplissage du message d'information
            $this->addFlash($result, $message);
            //redirection vers la route choisie
            return $this->redirectToRoute($route);
        }
        //sinon on affiche le formulaire
        else
        {
            //sinon, affichage du formulaire vide
            return $this->render('coffee/add.html.twig', ['form_coffee' => $form->createView() ] ); 
        }
    }
}
