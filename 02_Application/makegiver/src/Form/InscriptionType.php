<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [new NotBlank()],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [new NotBlank()],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [new NotBlank()],
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Je suis',
                'choices' => [
                    'Patient / Personne concernée' => 'Patient',
                    'Maker / Bricoleur'            => 'Maker',
                    'Soignant / Professionnel'     => 'Soignant',
                ],
                'expanded' => true,  // ← ajoute cette ligne
                'multiple' => false, // ← et celle-ci pour rester en choix unique
                'constraints' => [new NotBlank()],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'          => PasswordType::class,
                'mapped'        => false,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options'=> ['label' => 'Confirmer le mot de passe'],
                'constraints'   => [
                    new NotBlank(),
                    new Length(min: 8, minMessage: 'Le mot de passe doit faire au moins 8 caractères.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}