<?php

namespace App\Serializer\Normalizer;

use App\Entity\Tag;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;

class TagNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        $defaultContext = [
            AbstractObjectNormalizer::IGNORED_ATTRIBUTES => ['news']
        ];
        $context += $defaultContext;
        
        $classMetadataFactory = new ClassMetadataFactory(
            new AnnotationLoader(
                new AnnotationReader()
            )
        );
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);

        $serializer = new Serializer([
            new ObjectNormalizer(
                $classMetadataFactory,
                $metadataAwareNameConverter
            )
        ]);

        return $serializer->normalize($object, $format, array_unique($context, SORT_REGULAR));
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Tag;
    }
}
