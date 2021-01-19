<?php

namespace App\Form;

use App\Entity\Roasting;
use App\Entity\Coffee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FloatType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Formulaire pour ajouter une torréfaction
 */
class RoastingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        //les token csrf sont implémentés de base
        $builder
        //ajout des caractéristiques du champ
        ->add('name', TextType::class, [
            'label' => 'Nom de la torréfaction',
            'help' => 'champ obligatoire',
            'required' => true,
            'attr' => ['placeholder' => 'Saisir le nom de la nouvelle torréfaction'],
        ])
        ->add('save', SubmitType::class, [
            'label' => 'sauvegarder',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Roasting::class,
        ]);
    }
}