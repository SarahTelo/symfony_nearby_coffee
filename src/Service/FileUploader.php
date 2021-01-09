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
            dd('affichage de l\'excetpion, aller voir dans \'src\Service\FileUploader.php\': ', $e);
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
