<?php

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class TagIdsToTagTransformer  implements DataTransformerInterface
{
    public function __construct(
        readonly private EntityManagerInterface $em,
    ) {}

    public function transform($value): ?array
    {
        if (is_null($value)) {
            return null;
        }

        return array_map(fn (int $item) => $item->getId(), $value);
    }

    public function reverseTransform($value): ?array
    {
        if (is_null($value)) {
            return null;
        }

        return array_map(fn (string $item) => $this->em->getRepository(Tag::class)->find($item), $value);
    }
}