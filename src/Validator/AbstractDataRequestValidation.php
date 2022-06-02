<?php
namespace App\Validator;

use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validation;

abstract class AbstractDataRequestValidation
{
    public function validate($data, Constraint $constraint)
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($data, $constraint);

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