<?php

namespace App\Controller;

use App\Entity\Roasting;
use App\Form\RoastingType;
use App\Repository\RoastingRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * *Classe de gestion d'affichage des torréfactions disponible seulement aux utilisateurs authentifiés
 * 
 * @Route("/roasting", name="roasting")
 */
class RoastingController extends AbstractController
{
    /**
     * *Affichage de la liste des torréfactions
     * 
     * @Route("/list", name="_list", methods={"GET"})
     * 
     * @return void
     */
    public function roastingList(): Response
    {
        /** @var RoastingRepository $repository */
        //appel du repository
        $repository = $this->getDoctrine()->getRepository(Roasting::class);
        //appel de la fonction présente dans le repository
        $roastings = $repository->findAll();

        return $this->render('roasting/list.html.twig', [
            'roastings' => $roastings,
        ]);
    }

    /**
     * *Affichage du détail de la torréfaction
     * 
     * @Route("/{id}/detail", name="_detail", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @param int $id
     * @return void
     */
    public function roastingDetail(Roasting $roasting): Response
    {
        return $this->render('roasting/detail.html.twig', [
            'roasting' => $roasting,
        ]);
    }

    /**
     * *Ajout d'une torréfaction
     * 
     * @Route("/new", name="_new", methods={"GET", "POST"})
     * 
     * @param request $request
     * @return void
     */
    public function roastingNew(Request $request): Response
    {
        //création du moule "torréfaction"
        $roasting = new Roasting();
        //création du moule "formulaire" de type "Roasting"
        $form = $this->createForm(RoastingType::class, $roasting, [ 'attr' => ['novalidate' => 'novalidate'] ]);
        //stockage des données du formulaire dans la request
        $form->handleRequest($request);

        //-> si le formulaire a été validé, récupération des données et traitement de celles-ci
        if ($form->isSubmitted() && $form->isValid()) 
        {
            //les données du formulaire sont rentrées dans les propriétés de $roasting
            $roasting = $form->getData();
            //date de création
            $roasting->setCreatedAt( new \DateTime('now') );
            //stockage du nom de la torréfaction pour le réutiliser
            $roastingName = $roasting->getName();

            try {
                //appel de l'entity manager
                $em = $this->getDoctrine()->getManager();
                //sauvegarde
                $em->persist($roasting);
                //envoi à la BDD
                $em->flush();
                //remplissage des variables pour le message d'information d'état final
                $result = 'success';
                $message = "La torréfaction {$roastingName} a bien été ajoutée.";
                $route = 'roasting_detail';
            } catch (\Throwable $th) {
                //remplissage des variables pour le message d'information d'état final
                $result = 'danger';
                $message = "La torréfaction {$roastingName} n'a pas pu être ajoutée, veuillez contacter l'administrateur du site.";
                $route = 'roasting_new';
            }

            //remplissage du message d'information
            $this->addFlash($result, $message);
            //redirection vers la route choisie
            return $this->redirectToRoute($route, ['id' => $roasting->getId()]);
        }
        //-> sinon affichage du formulaire vide
        else
        {
            return $this->render('roasting/new.html.twig', [ 'form_roasting' => $form->createView() ] ); 
        }
    }

    /**
     * *Edition d'une torréfaction
     * 
     * @Route("/{id}/edit", name="_edit", methods={"GET", "PUT", "PATCH", "POST"}, requirements={"id"="\d+"})
     * 
     * @param int $id
     * @param request $request
     * @return void
     */
    public function roastingEdit(Request $request, int $id): Response
    {
        /** @var RoastingRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Roasting::class);
        $roasting = $repository->find($id);

        //les données de la torréfaction à éditer sont injecté dans le "formulaire" créé
        $form = $this->createForm(RoastingType::class, $roasting, [ 'attr' => ['novalidate' => 'novalidate'] ]);
        //stockage de l'ancien nom de la torréfaction
        $oldName = $roasting->getName();
        //stockage des données du formulaire dans la request
        $form->handleRequest($request);

        //-> si le formulaire a été validé, récupération des données et traitement de celles-ci
        if ($form->isSubmitted() && $form->isValid()) 
        {
            //date de mise à jour
            $roasting->setUpdatedAt( new \DateTime('now') );
            //stockage du nom de la torréfaction pour le réutiliser
            $roastingName = $roasting->getName();

            try {
                //appel de l'entity manager
                $em = $this->getDoctrine()->getManager();
                //envoi à la BDD
                $em->flush();
                //remplissage des variables pour le message d'information d'état final
                $result = 'success';
                $message = "La torréfaction {$oldName} a bien été modifiée.";
            } catch (\Throwable $th) {
                //remplissage des variables pour le message d'information d'état final
                $result = 'danger';
                $message = "La torréfaction {$roastingName} n'a pas pu être modifiée, veuillez contacter l'administrateur du site.";
            }

            //remplissage du message d'information
            $this->addFlash($result, $message);
            //redirection vers la route choisie
            return $this->redirectToRoute('roasting_detail', [ 'id' => $roasting->getId() ]);
        }
        //-> sinon affichage du formulaire avec les données de la torréfaction à éditer
        else
        {
            return $this->render('roasting/edit.html.twig', [ 
                'form_roasting_edit' => $form->createView(), 
                'name' => $roasting->getName() ]); 
        }
    }

    /**
     * *Suppression d'une torréfaction
     * 
     * @Route("/{id}/delete", name="_delete", methods={"GET", "DELETE"}, requirements={"id"="\d+"})
     * 
     * @param int $id
     * @return void
     */
    public function roastingDelete(int $id): Response
    {
        //reconnexion obligatoire si connexion précédente étaient en IS_AUTHENTICATED_REMEMBERED
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var RoastingRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Roasting::class);
        $roasting = $repository->find($id);
        //stockage du nom d'une torréfaction pour le réutiliser
        $roastingName = $roasting->getName();

        try {
            //appel de l'entity manager
            $em = $this->getDoctrine()->getManager();
            //sauvegarde
            $em->remove($roasting);
            //envoi à la BDD
            $em->flush();
            //remplissage des variables pour le message d'information d'état final
            $result = 'success';
            $message = "La torréfaction {$roastingName} a bien été supprimée.";
        } catch (\Throwable $th) {
            //remplissage des variables pour le message d'information d'état final
            $result = 'danger';
            $message = "La torréfaction {$roastingName} n'a pas pu être supprimée, car il reste des cafés dans cette catégorie.";
        }

        //remplissage du message d'information
        $this->addFlash($result, $message);
        //redirection vers la route choisie
        return $this->redirectToRoute('roasting_list');
    }
}
