<?php

namespace App\Service;

use Exception;
use App\Entity\News;
use App\Repository\NewsRepository;
use App\Serializer\Normalizer\TagNormalizer;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class NewsService 
{
    private $newsRepository;
    private $normalizer;
    private $tagNormalizer;

    public function __construct(
        NewsRepository $newsRepository,
        NormalizerInterface $normalizer,
        TagNormalizer $tagNormalizer
    ) 
    {
        $this->newsRepository = $newsRepository;
        $this->normalizer = $normalizer;
        $this->tagNormalizer = $tagNormalizer;
    }

    /**
     * Ограничения валидации
     * @return Assert\Collection
     */
    private function getConstraints(): Assert\Collection
    {   
        return new Assert\Collection([
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
    }

    /**
     * Валидация запроса
     * @var Request $data
     * @return void
     */
    public function requestValidation(
        $data
    ): void
    {   
        $validator = Validation::createValidator();
        $violations = $validator->validate($data, $this->getConstraints());

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
    
    /**
     * Получить новости
     * @param int $pg
     * @param int $on
     * @param string|null $dataFilter
     * @param array $tagIds
     * @return array
     */
    public function getNews(
        int $pg,
        int $on,
        string $dateFilter = null,
        array $tagIds
    ): array {
        return array_map(function(News $news) {
            return $this->newsNormalizer($news);
        }, $this->newsRepository->getNews($pg, $on, $dateFilter, $tagIds));
    }

    /**
     * Нормалайзер новости
     * @param News $news
     * @return array
     */
    public function newsNormalizer (
        News $news
    ): array{
        return $this->normalizer->normalize($news, null, [
            AbstractObjectNormalizer::CALLBACKS => [
                'tag' => function($object) {
                    return $this->tagNormalizer->normalize($object);
                }
            ]
        ]);
    }
}