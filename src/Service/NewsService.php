<?php

namespace App\Service;

use App\Entity\News;
use App\Entity\Tag;
use App\Repository\NewsRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class NewsService 
{
    private $newsRepository;
    private $normalizer;
    private $tagService;

    public function __construct(
        NewsRepository $newsRepository,
        NormalizerInterface $normalizer,
        TagService $tagService
    ) 
    {
        $this->newsRepository = $newsRepository;
        $this->normalizer = $normalizer;
        $this->tagService = $tagService;
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
                    return array_map(function (Tag $tag) {
                        return $this->tagService->tagNormalizer($tag);
                    }, $object->getValues());
                }
            ]
        ]);
    }
}