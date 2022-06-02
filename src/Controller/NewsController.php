<?php

namespace App\Controller;

use Exception;
use App\Entity\News;
use App\Service\NewsService;
use App\Service\ValidationService;
use App\Serializer\Normalizer\NewsNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/news", name="news")
 */
class NewsController extends AbstractController
{
    /**
     * Получить новости
     * @Route("/", name="news", methods={"GET"})
     * @param Request $request,
     * @param NewsService $newsService
     * @param ValidationService $validationService
     * @return Response
     */
    public function news(
        Request $request,
        NewsService $newsService,
        ValidationService $validationService
    ): Response
    {
        /**
         * @var array
         */
        $content = json_decode($request->getContent(), true);
        try {
            $validationService->requestValidation($content, $newsService->getConstraints());
        } catch(Exception $e) {
            return $this->json([
                "error" => unserialize($e->getMessage())
            ], $e->getCode());
        }

        /**
         * @var int
         */
        $pg = $content['pg'] ?? $this->getParameter('app.pg');
        /**
         * @var int
         */
        $on = $content['on'] ?? $this->getParameter('app.on');
        /**
         * @var string format: d-m-Y
         */
        $dateFilter = $content['dateFilter'] ?? null;
        /**
         * @var array
         */
        $tagIds = $content['tagIds'] ?? [];

        return $this->json([
            'list' => $newsService->getNews($pg, $on, $dateFilter, $tagIds)
        ]);
    }
    
    /**
     * Получить новость
     * @Route("/{news<\d+>}", name="one", methods={"GET"})
     * @param News $news
     * @return Response
     */
    public function one(
        ?News $news,
        NewsNormalizer $newsNormalizer
    ): Response
    {
        if (!$news) {
            return $this->json([
                'message' => 'Новость не найдена'
            ], 400);
        }

        return $this->json($newsNormalizer->normalize($news));
    }
}
