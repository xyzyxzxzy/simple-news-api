<?php

namespace App\Service;

use App\Entity\News;
use App\Repository\NewsRepository;
use App\Serializer\Normalizer\NewsNormalizer;

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