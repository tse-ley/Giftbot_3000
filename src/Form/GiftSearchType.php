<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiftSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'homme',
                    'Femme' => 'femme',
                    'Enfants' => 'enfants',
                    'Animaux' => 'animaux',
                ],
                'required' => false,
                'placeholder' => 'Catégorie'
            ])
            ->add('label', ChoiceType::class, [
                'choices' => [
                    'Soins' => 'soins',
                    'Loisirs' => 'loisirs',
                    'Technologie' => 'technologie',
                    'Maison' => 'maison',
                    'Accessoires' => 'accessoires',
                    'Vêtements' => 'vêtements',
                    'Cuisine' => 'cuisine',
                ],
                'required' => false,
                'placeholder' => 'Intérêt'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
