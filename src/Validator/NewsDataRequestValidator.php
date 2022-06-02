<?php
namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;

class NewsDataRequestValidator extends AbstractDataRequestValidation {
    
    public function validation(array $data) {
        $constraints = new Assert\Collection([
            'tagIds' => [
                new Assert\Optional([
                    new Assert\Type([
                        'type' => "array",
                        'message' => 'Должно быть типа массив',
                        
                    ]),
                    new Assert\All([
                        new Assert\Type([
                            'type' => "integer",
                            'message' => 'Должно быть целое чило'
                        ]),
                        new Assert\Positive([
                            'message' => 'Элементы массива должны быть больше 0'
                        ])
                    ])
                ])
            ],
            'dateFilter' => [
                new Assert\Optional([
                    new Assert\Type([
                        'type' => "string",
                        'message' => 'Должно быть строкой'
                    ]),
                    new Assert\DateTime([
                        'format' => "d-m-Y",
                        'message' => 'Используйте d-m-Y формат!'
                    ])
                ]),
            ],
            'pg' => [
                new Assert\Optional([
                    new Assert\Type([
                        'type' => "integer",
                        'message' => 'Должно быть целое чило'
                    ]),
                    new Assert\Positive([
                        'message' => 'Число должно быть больше 0'
                    ])
                ])
            ],
            'on' => [
                new Assert\Optional([
                    new Assert\Type([
                        'type' => "integer",
                        'message' => 'Должно быть целое чило'
                    ]),
                    new Assert\Positive([
                        'message' => 'Число должно быть больше 0'
                    ])
                ])
            ]
        ]);
    
        $this->validate($data, $constraints);
    }
}