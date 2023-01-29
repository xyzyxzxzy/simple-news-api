<?php

namespace App\Validator;

use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractDataRequestValidation
{
    private $validator;

    public function __construct(
        ValidatorInterface $validator
    ) {
        $this->validator = $validator;
    }

    public function validate($data, Constraint $constraint): void
    {
        $violations = $this->validator->validate($data, $constraint);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $key => $violation) {
                if ($key % 2 === 0) {
                    $errors[preg_replace('/[[\]\][0-9]+/', '', $violation->getPropertyPath())] = $violation->getMessage();
                }
            }
            throw new Exception(serialize($errors), 400);
        }
    }
}
