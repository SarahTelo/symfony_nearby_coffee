<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoffeeController extends AbstractController
{
    /**
     * @Route("/coffee", name="coffee")
     */
    public function index(): Response
    {
        return $this->render('coffee/index.html.twig', [
            'controller_name' => 'CoffeeController',
        ]);
    }
}
