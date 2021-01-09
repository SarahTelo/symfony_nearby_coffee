<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Form\GalleryType;
use App\Repository\GalleryRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use SluggerService;
use App\Service\FileUploader;

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

    /**
     * *Ajout d'une image
     * 
     * @Route("/admin/new", name="_new", methods={"GET", "POST"})
     * 
     * @param request $request
     * @return void
     */
    public function galleryNew(Request $request, FileUploader $fileUploader): Response
    {
        //création du moule "image"
        $gallery = new Gallery();
        //création du moule "formulaire" de type "gallery"
        $form = $this->createForm(GalleryType::class, $gallery, [ 'attr' => ['novalidate' => 'novalidate'] ]);
        //stockage des données du formulaire dans la request
        $form->handleRequest($request);

        //-> si le formulaire a été validé, récupération des données et traitement de celles-ci
        if ($form->isSubmitted() && $form->isValid()) 
        {
            //les données du formulaire sont rentrées dans les propriétés de $gallery
            $gallery = $form->getData();
            //date de création
            $gallery->setCreatedAt( new \DateTime('now') );
            //stockage du nom de l'image pour le réutiliser
            $galleryName = $gallery->getName();

            //instancier le service
            $slugger = new SluggerService();
            //appel de la fonction du service
            $gallerySlug = $slugger->slugify($galleryName);
            //sauvegarde du nom en format slug
            $gallery->setSlug($gallerySlug);

            //*possible si création d'un constructeur dans galleryController et ajout de la propriété privée "$slugger"
            //*$gallery->setSlug($this->slugger->slugify($galleryName));

            /** @var UploadedFile $imageFile */
            //traitement de l'ajout du fichier de l'image
            $imageFile = $form->get('image')->getData();
            //appel du service d'upload
            $imageFileName = $fileUploader->upload($imageFile);
            //ajout du chemin dans le champ "way" de la bdd
            $gallery->setWay($imageFileName);

            try {
                //appel de l'entity manager
                $em = $this->getDoctrine()->getManager();
                //sauvegarde
                $em->persist($gallery);
                //envoi à la BDD
                //! à décommenter pour sauvegarder en BDD
                $em->flush();
                //remplissage des variables pour le message d'information d'état final
                $result = 'success';
                $message = "L'image {$galleryName} a bien été ajouté";
                $route = 'gallery_list';
            } catch (\Throwable $th) {
                //remplissage des variables pour le message d'information d'état final
                $result = 'danger';
                $message = "L'image {$galleryName} n'a pas pu être ajouté, veuillez contacter l'administrateur du site.";
                $route = 'gallery_new';
                $gallerySlug = null;
            }

            //remplissage du message d'information
            $this->addFlash($result, $message);
            //redirection vers la route choisie
            return $this->redirectToRoute($route, ['slug' => $gallerySlug]);
        }
        //-> sinon affichage du formulaire vide
        else
        {
            return $this->render('gallery/new.html.twig', [ 'form_gallery' => $form->createView() ] );
        }
    }

    /**
     * *Edition d'une image
     * 
     * @Route("/admin/edit/{slug}", name="_edit", methods={"GET", "PUT", "PATCH", "POST"})
     * 
     * @param request $request
     * @param gallery => (injection de dépendance)
     * @return void
     */
    public function galleryEdit(Request $request, gallery $gallery, FileUploader $fileUploader): Response
    {
        //l'image à supprimer a été trouvé par l'injection de dépendance: "gallery $gallery"
        //il n'est pas nécessaire d'appeler le repository
        //méthode POST utilisée (plus rapide)

        //les données de l'image à éditer sont injectées dans le "formulaire" créé
        $form = $this->createForm(GalleryType::class, $gallery, [ 'attr' => ['novalidate' => 'novalidate'] ]);
        //stockage des données du formulaire dans la request
        $form->handleRequest($request);

        //-> si le formulaire a été validé, récupération des données et traitement de celles-ci
        if ($form->isSubmitted() && $form->isValid()) 
        {
            //date de mise à jour
            $gallery->setUpdatedAt( new \DateTime('now') );
            //stockage du nom de l'image pour le réutiliser
            $galleryName = $gallery->getName();

            //instancier le service
            $slugger = new SluggerService();
            //appel de la fonction du service
            $gallerySlug = $slugger->slugify($galleryName);
            //sauvegarde du nom en format slug
            $gallery->setSlug($gallerySlug);

            /** @var UploadedFile $imageFile */
            //traitement de l'ajout du fichier de l'image
            $imageFile = $form->get('image')->getData();
            //mise en conditionnel afin d'éviter de devoir re télécharger l'image
            if ($imageFile) {
                //appel du service d'upload
                $imageFileName = $fileUploader->upload($imageFile);
                //ajout du chemin dans le champ "way" de la bdd
                $gallery->setWay($imageFileName);
            }
            
            try {
                //appel de l'entity manager
                $em = $this->getDoctrine()->getManager();
                //envoi à la BDD
                $em->flush();
                //remplissage des variables pour le message d'information d'état final
                $result = 'success';
                $message = "L'image {$galleryName} a bien été modifié";
            } catch (\Throwable $th) {
                //remplissage des variables pour le message d'information d'état final
                $result = 'danger';
                $message = "L'image {$galleryName} n'a pas pu être modifié, veuillez contacter l'administrateur du site.";
            }

            //remplissage du message d'information
            $this->addFlash($result, $message);
            //redirection vers la route choisie
            return $this->redirectToRoute('gallery_list', [ 'slug' => $gallery->getSlug() ]);
        }
        //-> sinon affichage du formulaire avec les données de l'image à éditer
        else
        {
            return $this->render('gallery/edit.html.twig', [ 
                'form_gallery_edit' => $form->createView(), 
                'name' => $gallery->getName() ]); 
        }
    }
}
