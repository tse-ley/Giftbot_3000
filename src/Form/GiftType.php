<?php

namespace App\Form;

use App\Entity\Gift;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GiftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('price', NumberType::class, [
                'scale' => 2,
                'required' => true,
            ])
            ->add('category', ChoiceType::class, [
                'choices'  => [
                    'Soins' => 'soins',
                    'Loisirs' => 'loisirs',
                    'Technologie' => 'technologie',
                    'Maison' => 'maison',
                    'Accessoires' => 'accessoires',
                    'Vêtements' => 'vêtements',
                    'Cuisine' => 'cuisine'
                ],
                'required' => true,
            ])
            ->add('stock_quantity', NumberType::class, [
                'required' => true,
            ])
            ->add('image_url', TextType::class, [
                'required' => true,
            ])
            // Add other fields as needed, e.g. image, etc.
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gift::class,
        ]);
    }
}
