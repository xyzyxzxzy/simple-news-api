<?php

namespace App\Form\Tag;

use App\Form\DataTransformer\ClearStringTransformer;
use App\Form\Model\Tag\TagUpdateModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagUpdateForm extends AbstractType
{
    public function __construct(
        readonly private ClearStringTransformer $clearStringTransformer,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'required' => false,
            ]
        );

        $builder->get('name')
            ->addModelTransformer($this->clearStringTransformer);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('csrf_protection', false);
        $resolver->setDefault('data_class', TagUpdateModel::class);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}