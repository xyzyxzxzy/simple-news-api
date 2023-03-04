<?php

namespace App\Form\News;

use App\Form\DataTransformer\ClearStringTransformer;
use App\Form\DataTransformer\StringToArrayOfIntegerValuesTransformer;
use App\Form\DataTransformer\TagIdsToTagTransformer;
use App\Form\Model\News\NewsCreateModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsCreateForm extends AbstractType
{
    public function __construct(
        readonly private ClearStringTransformer $clearStringTransformer,
        private readonly TagIdsToTagTransformer $tagIdsToTagTransformer,
        private readonly StringToArrayOfIntegerValuesTransformer $stringToArrayOfIntegerValuesTransformer,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'required' => false,
            ]
        )
        ->add(
            'content',
            TextType::class,
            [
                'required' => false,
            ]
        )
        ->add(
            'preview',
            TextType::class,
            [
                'required' => false,
            ]
        )
        ->add(
            'tags',
            TextType::class,
        );

        $builder->get('tags')
            ->addModelTransformer($this->stringToArrayOfIntegerValuesTransformer);
        $builder->get('tags')
            ->addModelTransformer($this->tagIdsToTagTransformer);

        $builder->get('name')
            ->addModelTransformer($this->clearStringTransformer);

        $builder->get('content')
            ->addModelTransformer($this->clearStringTransformer);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('csrf_protection', false);
        $resolver->setDefault('data_class', NewsCreateModel::class);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}