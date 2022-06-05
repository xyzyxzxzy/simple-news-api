<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;

class TagValidator extends AbstractDataRequestValidation
{
    /**
     * Валидатор полей тега
     * @param array $data
     * @param array $options
     * @return void
     */
    public function validation(array $data, array $options = array()): void
    {
        if (empty($options)) {
            $options = [
                'allowMissingFields' => false,
                'allowExtraFields' => true
            ];
        }

        $constraints = new Assert\Collection([
            'fields' => [
                'name' => [
                    new Assert\Type([
                        'type' => 'string',
                        'message' => 'Это поле должно быть строкой',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'minMessage' => 'Это поле должно содержать минимум 2 символа',
                        'max' => 20,
                        'maxMessage' => 'Это поле должно содержать максимум 20 символов'
                    ])
                ]
            ],
            'missingFieldsMessage' => 'Это поле обязательно'
        ] + $options);

        $this->validate($data, $constraints);
    }
}
