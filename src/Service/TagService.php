<?php

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Serializer\Normalizer\TagNormalizer;


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