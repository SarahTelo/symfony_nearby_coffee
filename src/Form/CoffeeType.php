<?php

namespace App\Form;

use App\Entity\Coffee;
use App\Entity\Roasting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * *Formulaire d'ajout d'un café
 */
class CoffeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
        //ajout des caractéristique du champ
        ->add('name', TextType::class, [
            'label' => 'Nom du café',
            'help' => 'champ obligatoire',
            'required' => true,
            'attr' => ['placeholder' => 'Saisir le nom du nouveau café'],
        ])
        ->add('country', TextType::class, [
            'label' => 'Nom du pays',
            'help' => 'champ obligatoire',
            'required' => true,
            'attr' => ['placeholder' => 'Saisir le pays du café'],
        ])
        ->add('price', NumberType::class, [
            'label' => 'Prix du café',
            //'help' => 'test',
            'required' => false,
            'attr' => ['placeholder' => 'Saisir le prix du café'],
        ])
        ->add('roasting', EntityType::class, [
            'label' => 'Choisir une torréfaction',
            'class' => Roasting::class,
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => true,
            'required' => true,
            'help' => 'choix obligatoire',
        ])
        ->add('save', SubmitType::class, [
            'label' => 'sauvegarder',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coffee::class,
        ]);
    }
}