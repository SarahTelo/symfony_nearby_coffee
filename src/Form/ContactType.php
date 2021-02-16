<?php

namespace App\Form;

use Doctrine\Inflector\Rules\Pattern;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, [
                'label' => 'Votre nom',
                'help' => 'champ obligatoire',
                'required' => true,
                'attr' => ['placeholder' => 'Saisir votre nom'],
                'constraints' => [
                    new NotBlank(['message' => 'Champ obligatoire']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Minimum {{ limit }} caractères.',
                        'max' => 500,
                        'maxMessage' => 'Maximum {{ limit }} caractères',
                    ]),
                    new Regex([
                        'pattern' => "[[a-zA-Z]]",
                        'match' => true,
                        'message' => 'Votre nom doit contenir au minimum un caractère alphabétique.'
                    ]),
                    new Regex([
                        'pattern' => "[[0-9]]",
                        'match' => false,
                        'message' => 'Votre nom ne doit pas contenir de chiffre.'
                    ]),
                    new Regex([
                        'pattern' => "[[=%\$<>*+\}\{\\\/\]\[;()]]",
                        'match' => false,
                        'message' => 'Votre nom ne doit pas contenir les caractères spéciaux suivants: = % $ < > * + } { \ / ] [ ; ( )'
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'help' => 'champ obligatoire',
                'attr' => ['placeholder' => 'Saisir votre adresse mail'],
                'constraints' => [
                    new NotBlank(['message' => 'Champ obligatoire']),
                    new Length([
                        'min' => 3,
                        'minMessage' => '3',
                        'max' => 500,
                        'maxMessage' => '500',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'required' => false,
                'help' => 'maximum 500 caratères',
                'attr' => ['placeholder' => 'Saisir votre message'],
                'constraints' => [
                    new NotBlank(['message' => 'Champ obligatoire']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Minimum {{ limit }} caractères.',
                        'max' => 500,
                        'maxMessage' => 'Maximum {{ limit }} caractères',
                    ]),
                    new Regex([
                        'pattern' => "[[a-zA-Z]]",
                        'match' => true,
                        'message' => 'Votre message doit contenir au minimum un caractère alphabétique.'
                    ]),
                    new Regex([
                        'pattern' => "[[=%\$<>*+\}\{\\\/\]\[;]]",
                        'match' => false,
                        'message' => 'Votre nom ne doit pas contenir les caractères spéciaux suivants: = % $ < > * + } { \ / ] [ ;'
                    ]),
                ],
            ])
            ->add('envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
