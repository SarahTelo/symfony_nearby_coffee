<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoastingController extends AbstractController
{
    /**
     * @Route("/roasting", name="roasting")
     */
    public function index(): Response
    {
        return $this->render('roasting/index.html.twig', [
            'controller_name' => 'RoastingController',
        ]);
    }
}
