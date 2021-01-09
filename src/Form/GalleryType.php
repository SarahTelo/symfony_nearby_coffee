<?php

namespace App\Form;

use App\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\ImageType;
use Symfony\Component\Validator\Constraints\Image;

/**
 * *Formulaire d'ajout d'un image
 */
class GalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        //les token csrf sont implémentés de base
        $builder
        //ajout des caractéristiques du champ
        ->add('name', TextType::class, [
            'label' => 'Nom de l\'image',
            'help' => 'champ obligatoire',
            'required' => true,
            'attr' => ['placeholder' => 'Saisir le nom de l\'image'],
        ])
        ->add('image', FileType::class, [
            'label' => 'Ajoutez l\'image',
            // TODO : le placeholder avec le chemin du fichier quand on l'ajoute
            //mapped à false car l'image ne correspond pas à une propriété de l'entité
            'mapped' => false,
            //required à false afin de pouvoir éditer un image sans avoir à la télécharger à nouveau
            'required' => false,
            'constraints' => 
                new Image ([
                    'minWidth' => 200,
                    'minWidthMessage' => 'L\'image doit faire au minimum 200 pixels.',
                    'maxWidth' => 800,
                    'maxWidthMessage' => 'L\'image ne doit pas dépasser 200 x 800 pixels.',
                    'minHeight' => 200,
                    'minHeightMessage' => 'L\'image doit faire au minimum 200 pixels.',
                    'maxHeight' => 800,
                    'maxHeightMessage' => 'L\'image ne doit pas dépasser 200 x 800 pixels.',
                    'maxSize' => 1000000,
                    'maxSizeMessage' => 'L\'image ne doit pas dépasser 1 Mo (1000000 Ko).',
                ]),
        ])
        ->add('save', SubmitType::class, [
            'label' => 'sauvegarder',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
        ]);
    }
}