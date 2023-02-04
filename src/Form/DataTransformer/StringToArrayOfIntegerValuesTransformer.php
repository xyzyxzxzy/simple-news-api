<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class StringToArrayOfIntegerValuesTransformer  implements DataTransformerInterface
{
    public function transform($value): ?array
    {
        if (is_null($value)) {
            return null;
        }

        return array_map(fn (int $item) => (string)$item, $value);
    }

    public function reverseTransform($value): ?array
    {
        if (is_null($value)) {
            return null;
        }

        $value = explode(',', $value) ?? $value;

        return array_map(fn (string $item) => (int)$item, $value);
    }
}