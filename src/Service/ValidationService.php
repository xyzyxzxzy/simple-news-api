<?php

namespace App\Service;

use Exception;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Collection;

class ValidationService 
{
    /**
     * Валидация запроса
     * @var Request $data
     * @var Collection $constraints
     * @return void
     */
    public function requestValidation(
        $data,
        $constraints
    ): void
    {   
        $validator = Validation::createValidator();
        $violations = $validator->validate($data, $constraints);

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