<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * *Classe de gestion d'affichage des cafés servis
 * @Route("/coffee", name="coffee")
 */
class CoffeeController extends AbstractController
{
    /**
     * *Affichage de la liste des cafés avec les détails
     * @Route("/list", name="_list")
     */
    public function coffeeList(): Response
    {
        return $this->render('coffee/index.html.twig', [
            'controller_name' => 'CoffeeController',
        ]);
    }

    /**
     * *Ajout d'un café
     * @Route("/add", name="_add")
     */
    public function coffeeAdd(Request $request): Response
    {
        //récupération des données envoyées par le forumlaire symfony
        //todo

        return $this->render('coffee/index.html.twig', [
            'controller_name' => 'CoffeeController',
        ]);
    }
}
