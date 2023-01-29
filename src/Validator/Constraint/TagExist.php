<?php

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class TagExist extends Constraint
{
    public $message = 'Тега с id {{ value }} нет';

    public function validatedBy(): string
    {
        return 'tag.exist.validator';
    }
}
