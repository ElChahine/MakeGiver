<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom *',
                'constraints' => [new NotBlank(message: 'Veuillez entrer votre prénom')],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom *',
                'constraints' => [new NotBlank(message: 'Veuillez entrer votre nom')],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'constraints' => [new NotBlank(message: 'Veuillez entrer un email valide')],
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo *',
                'constraints' => [
                    // CORRECTION ICI : On utilise "message:" au lieu de "null,"
                    new NotBlank(message: 'Veuillez entrer un pseudo'), 
                ],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Numéro de téléphone',
                'required' => false,
            ])
            ->add('region', ChoiceType::class, [
                'label' => 'Région *',
                'choices' => [
                    'Auvergne-Rhône-Alpes' => 'Auvergne-Rhône-Alpes',
                    'Bourgogne-Franche-Comté' => 'Bourgogne-Franche-Comté',
                    'Bretagne' => 'Bretagne',
                    'Centre-Val de Loire' => 'Centre-Val de Loire',
                    'Corse' => 'Corse',
                    'Grand Est' => 'Grand Est',
                    'Hauts-de-France' => 'Hauts-de-France',
                    'Île-de-France' => 'Île-de-France',
                    'Normandie' => 'Normandie',
                    'Nouvelle-Aquitaine' => 'Nouvelle-Aquitaine',
                    'Occitanie' => 'Occitanie',
                    'Pays de la Loire' => 'Pays de la Loire',
                    'Provence-Alpes-Côte d\'Azur' => 'Provence-Alpes-Côte d\'Azur',
                ],
                'placeholder' => 'Choisissez votre région',
                'constraints' => [new NotBlank(message: 'Veuillez sélectionner une région')],
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Je suis *',
                'choices' => [
                    'Membre (Personne concernée)' => 'Membre',
                    'Maker / Bricoleur'           => 'Maker',
                    'Soignant / Professionnel'    => 'Soignant',
                ],
                'constraints' => [new NotBlank(message: 'Veuillez choisir un rôle')],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'          => PasswordType::class,
                'mapped'        => false,
                'first_options' => ['label' => 'Mot de passe *'],
                'second_options'=> ['label' => 'Confirmer le mot de passe *'],
                'constraints'   => [
                    new NotBlank(message: 'Le mot de passe est obligatoire'),
                    // CORRECTION ICI : On enlève les crochets [] et on utilise "min:" et "minMessage:"
                    new Length(
                        min: 8, 
                        minMessage: 'Le mot de passe doit faire au moins 8 caractères.'
                    ),
                ],
            ])
            ->add('accepte_contact_public', CheckboxType::class, [
                'label'    => 'J\'accepte que mon profil soit visible par la communauté',
                'required' => false,
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