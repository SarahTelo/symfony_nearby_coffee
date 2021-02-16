<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use SluggerService;

/**
 * *Service permettant l'upload des fichiers
 */
class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file)
    {
        //on récupère les données du fichier d'origine
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        //instancier le service
        $slugger = new SluggerService();
        //appel de la fonction du service
        $safeFilename = $slugger->slugify($originalFilename);
        //réécriture du nom du fichier
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            //transfert vers le dossier cible (config\services.yaml)
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            //création d'une exception de type "fichier"
            //! TODO : arriver à l'exception: à traiter avec test unitaire?
            dd('affichage de l\'excetpion, aller voir dans \'src\Service\FileUploader.php\': UPLOAD : ', $e);
        }

        return $fileName;
    }

    // todo : à rendre universelle pour la suppression des fichiers
    /**
     * *Suppression du fichier physique d'une photo
     *
     * @param string $fileName (propriété $way)
     * @return boolean
     */
    public function deleteFileGallery (string $fileName) 
    {
        //récupérer le chemin du dossier de l'image
        $path = $this->getTargetDirectory();
        //création du chemin complet
        $pathToRemove = $path . "/" . $fileName;

        //todo vérifier qu'il n'est pas un fichier système?
        
        //effacement du fichier physique s'il existe et s'il est dans le dossier spécifique
        if (file_exists($pathToRemove) && str_contains($path, '/public/images')) {
            try {
                //suppression du fichier
                unlink($pathToRemove);
            } catch (FileException $e) {
                //! TODO : arriver à l'exception: à traiter avec test unitaire?
                dd('affichage de l\'excetpion, aller voir dans \'src\Service\FileUploader.php\': DELETE : ', $e);
            }
            return true;
        } else {
            return false;
        }
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
