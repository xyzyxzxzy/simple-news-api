<?php

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Serializer\Normalizer\TagNormalizer;
use Symfony\Component\Validator\Constraints as Assert;


class TagService 
{
    private $tagRepository;
    private $tagNormalizer;

    public function __construct(
        TagRepository $tagRepository,
        TagNormalizer $tagNormalizer
    ) 
    {
        $this->tagRepository = $tagRepository;
        $this->tagNormalizer = $tagNormalizer;
    }

    /**
     * Ограничения валидации
     * @return Assert\Collection
     */
    public function getConstraints(): Assert\Collection
    {   
        return new Assert\Collection([
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
     * Получить теги
     * @param int $pg
     * @param int $on
     * @return array
     */
    public function getTags(
        int $pg,
        int $on
    ): array {
        return array_map(function(Tag $tag) {
            return $this->tagNormalizer->normalize($tag);
        }, $this->tagRepository->getTags($pg, $on));
    }
}