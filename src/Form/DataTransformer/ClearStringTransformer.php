<?php

namespace App\Form\DataTransformer;

use App\Service\UtilsService;
use Symfony\Component\Form\DataTransformerInterface;

class ClearStringTransformer  implements DataTransformerInterface
{
    public function __construct(
        private readonly UtilsService $utilsService,
    ) {}

    public function transform($value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        return $value;
    }

    public function reverseTransform($value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        return $this->utilsService->convertString($value);
    }
}