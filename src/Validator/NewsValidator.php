<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraint as CustomConstraint;

class NewsValidator extends AbstractDataRequestValidation
{
    /**
     * Валидатор полей новости
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
                        'min' => 4,
                        'minMessage' => 'Это поле должно содержать минимум 4 символа',
                        'max' => 100,
                        'maxMessage' => 'Это поле должно содержать максимум 100 символов'
                    ])
                ],
                'preview' => [
                    new Assert\NotBlank([
                        'message' => 'Это поле не должно быть пустым',

                    ]),
                    new Assert\Type([
                        'type' => 'string',
                        'message' => 'Это поле должно быть строкой',
                    ]),
                    new Assert\File([
                        'maxSize' => '1M',
                        'maxSizeMessage' => 'Файл слишком большой ({{ size }} {{ suffix }}). Допустимый максимальный размер: {{ limit }} {{ suffix }}',
                        'mimeTypes' => ['image/jpeg', 'image/jpg', 'image/x-png'],
                        'mimeTypesMessage' => 'Недопустимый MIME-тип файла ({{ type }}). Допустимые типы mime: {{ types }}',
                        'notFoundMessage' => 'Не удалось найти файл'
                    ])
                ],
                'content' => [
                    new Assert\Type([
                        'type' => 'string',
                        'message' => 'Это поле должно быть строкой'
                    ]),
                    new Assert\Length([
                        'min' => 50,
                        'minMessage' => 'Это поле должно содержать минимум 50 символов'
                    ])
                ],
                'tagIds' => [
                    new Assert\NotBlank([
                        'message' => 'Массив не должен быть пустым',

                    ]),
                    new Assert\Unique([
                        'message' => 'Массив должен содержать только уникальные элементы'

                    ]),
                    new Assert\Type([
                        'type' => "array",
                        'message' => 'Должно быть типа массив',

                    ]),
                    new Assert\All([
                        new Assert\Type([
                            'type' => "integer",
                            'message' => 'id тега должен быть целым чилом'
                        ]),
                        new Assert\Positive([
                            'message' => 'id тега должен быть больше 0'
                        ]),
                        new CustomConstraint\TagExist()
                    ])
                ]
            ],
            'missingFieldsMessage' => 'Это поле обязательно'
        ] + $options);

        $this->validate($data, $constraints);
    }
}
