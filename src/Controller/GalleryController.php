<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Form\GalleryType;
use App\Repository\GalleryRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use SluggerService;

/**
 * *Classe de gestion d'affichage des images
 * 
 * @Route("/gallery", name="gallery")
 */
class GalleryController extends AbstractController
{
    /**
     * *Affichage de la liste des images
     * 
     * @Route("/list", name="_list", methods={"GET"})
     * 
     * @return void
     */
    public function galleryList(): Response
    {
        /** @var GalleryRepository $repository */
        //appel du repository
        $repository = $this->getDoctrine()->getRepository(Gallery::class);
        //appel de la fonction présente dans le repository
        $gallery = $repository->findAll();

        return $this->render('gallery/list.html.twig', [
            'gallery' => $gallery,
        ]);
    }
}
