<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class DistanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('latitude', NumberType::class, [
                'label' => 'Votre latitude',
                'attr' => [
                    'placeholder' => 'Exemple : 50.6333',
                ],
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Votre longitude',
                'attr' => [
                    'placeholder' => 'Exemple : 3.0586',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true, // Activer la protection CSRF
        ]);
    }
}
