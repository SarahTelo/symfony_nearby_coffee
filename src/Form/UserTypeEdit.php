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

use Symfony\Component\Security\Core\Security;

/**
 * *Formulaire d'édition d'un utilisateur
 */
class UserTypeEdit extends AbstractType
{

    private $security;

    /**
     * * Constructeur afin de pouvoir récupérer l'utilisateur courant (getUser() ne fonctionne pas)
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $userRoles = $this->security->getUser()->getRoles();
        $hasSuperAccess = in_array('ROLE_SUPER_ADMIN', $userRoles);
        $hasAccess = in_array('ROLE_ADMIN', $userRoles);
        
        /** @var UserInferface $user */
        $currentUserId = $this->security->getUser()->getId();
        $TargetUserId = $options['attr']['data'];

        if ($hasSuperAccess) 
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
                    'Super administrateur' => 'ROLE_SUPER_ADMIN',
                ],
                //!attention: ne protège pas contre la suppression via F12 en html
                'choice_attr' => [
                    'Super administrateur' => ['disabled' => true]
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'sauvegarder',
            ])
            ;
        }
        elseif ($hasAccess && ( $currentUserId !== $TargetUserId )) 
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
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'sauvegarder',
            ])
            ;
        }
        else 
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
            ->add('save', SubmitType::class, [
                'label' => 'sauvegarder',
            ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}