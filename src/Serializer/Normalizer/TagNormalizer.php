<?php

namespace App\Serializer\Normalizer;

use App\Entity\Tag;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class TagNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return array_map(function(Tag $tag) {
            return [
                'id' => $tag->getId(),
                'name' => $tag->getName()
            ];
        }, $object->getValues());
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Tag;
    }
}
