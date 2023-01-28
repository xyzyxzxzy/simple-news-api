<?php

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TagExist extends Constraint
{
    /**
     * @string $message
     */
    public $message = 'Тега с id {{ value }} нет';

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return 'tag.exist.validator';
    }
}
