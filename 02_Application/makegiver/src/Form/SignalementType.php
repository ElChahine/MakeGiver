<?php

namespace App\Form;

use App\Entity\Signalements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SignalementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeContenu', HiddenType::class, [
                'constraints' => [new NotBlank(message: 'Type de contenu manquant.')],
            ])
            ->add('contenuId', HiddenType::class, [
                'required'   => false,
                'empty_data' => null,
            ])
            ->add('raison', TextareaType::class, [
                'label' => 'Raison du signalement *',
                'attr'  => ['rows' => 5, 'placeholder' => 'Expliquez pourquoi vous signalez ce contenu...'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez indiquer la raison du signalement'),
                    new Length(min: 10, minMessage: 'La raison doit faire au moins 10 caractères.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Signalements::class,
        ]);
    }
}
