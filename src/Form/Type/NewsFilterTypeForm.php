<?php

namespace App\Form\Type;

use App\Form\DataTransformer\StringToArrayOfIntegerValuesTransformer;
use App\Form\Model\NewsFilterModel;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsFilterTypeForm extends MainFilterTypeForm
{
    public function __construct(
        private readonly StringToArrayOfIntegerValuesTransformer $stringToArrayOfIntegerValuesTransformer,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add(
            'dateFilter',
            TextType::class,
            [
                'required' => false,
            ]
        )
        ->add(
            'tagIds',
            TextType::class,
            [
                'required' => false,
            ]
        );

        $builder->get('tagIds')->addModelTransformer($this->stringToArrayOfIntegerValuesTransformer);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', NewsFilterModel::class);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}