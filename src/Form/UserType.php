<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 * *Formulaire d'ajout d'un utilisateur
 */
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        //les token csrf sont implémentés de base
        $builder
        //ajout des caractéristiques du champ
        ->add('firstname', TextType::class, [
            'label' => 'Prénom',
            'help' => 'champ obligatoire',
            'required' => true,
            'attr' => ['placeholder' => 'Saisir le prénom'],
        ])
        ->add('lastname', TextType::class, [
            'label' => 'Nom',
            'help' => 'champ obligatoire',
            'required' => true,
            'attr' => ['placeholder' => 'Saisir le nom'],
        ])
        ->add('email', EmailType::class, [
            'help' => 'champ obligatoire',
            'attr' => ['placeholder' => 'Saisir l\'email'],
        ])
        //!->add('status', TextType::class, [ 'label' => '??' ])
        //ChoiceType: choix multiple
        ->add('roles', ChoiceType::class, [
            'label' => 'Rôle(s) attribué(s)',
            //multiple à true : car les choix ne viennent pas d'une entité
            'multiple' => true,
            'expanded' => true,
            'required' => true,
            'help' => 'champ obligatoire',
            'choices' => [
                'Responsable'    => 'ROLE_RESPONSIBLE', 
                'Administrateur' => 'ROLE_ADMIN',
            ],
        ])
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots passent doivent être identiques.',
            'required' => true,
            'first_options'  => [
                'label' => 'Saisissez votre mot de passe',
                'help'  => 'Minimum 8 caractères dont 1 majuscule, 1 minuscule et 1 chiffre'],
            'second_options' => [
                'label' => 'Saisir à nouveau votre mot de passe'],
        ])
        ->add('save', SubmitType::class, [
            'label' => 'sauvegarder',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}