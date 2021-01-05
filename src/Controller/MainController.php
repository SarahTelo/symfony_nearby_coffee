<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;

//TODO : ajouter la fonction de la route de contact => envoi de mail

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_home")
     */
    public function home(Request $request): Response
    {
        //dd($request);
        return $this->render('main/home.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
