<?php

namespace App\Serializer\Normalizer;

use App\Entity\News;
use App\Entity\Tag;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;

class NewsNormalizer implements NormalizerInterface
{
    private $tagNormalizer;

    public function __construct(
        TagNormalizer $tagNormalizer
    )
    {
        $this->tagNormalizer = $tagNormalizer;
    }

    public function normalize($object, $format = null, array $context = array())
    {
        $defaultContext = [
            AbstractObjectNormalizer::CALLBACKS => [
                'tag' => fn ($object) => array_map(fn (Tag $tag) =>
                    $this->tagNormalizer->normalize($tag), $object->getValues()
                )
            ],
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'
        ];
        $context += $defaultContext;

        $classMetadataFactory = new ClassMetadataFactory(
            new AnnotationLoader(
                new AnnotationReader()
            )
        );

        $serializer = new Serializer([
            new DateTimeNormalizer(),
            new ObjectNormalizer($classMetadataFactory)
        ]);

        return $serializer->normalize($object, $format, array_unique($context, SORT_REGULAR));
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof News;
    }
}
