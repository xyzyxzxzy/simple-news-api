<?php

namespace App\Serializer\Normalizer;

use App\Entity\Tag;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class TagNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName()
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Tag;
    }
}
