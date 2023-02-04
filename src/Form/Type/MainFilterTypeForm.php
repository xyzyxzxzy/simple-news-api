<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MainFilterTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'pg',
            IntegerType::class,
            [
                'required' => false,
            ]
        )
        ->add(
            'on',
            IntegerType::class,
            [
                'required' => false,
            ]
        );
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('csrf_protection', false);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}