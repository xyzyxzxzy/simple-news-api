<?php

namespace App\Service;

use App\Entity\News;
use App\Repository\NewsRepository;
use App\Serializer\Normalizer\NewsNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

class NewsService 
{
    private $newsRepository;
    private $newsNormalizer;

    public function __construct(
        NewsRepository $newsRepository,
        NewsNormalizer $newsNormalizer
    ) 
    {
        $this->newsRepository = $newsRepository;
        $this->newsNormalizer = $newsNormalizer;
    }

    /**
     * Ограничения валидации
     * @return Assert\Collection
     */
    public function getConstraints(): Assert\Collection
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
            return $this->newsNormalizer->normalize($news);
        }, $this->newsRepository->getNews($pg, $on, $dateFilter, $tagIds));
    }
}