<?php

namespace App\Controller;

use Exception;
use App\Entity\News;
use App\Entity\User;
use App\Service\NewsService;
use App\Validator\NewsFilterValidator;
use App\Serializer\Normalizer\NewsNormalizer;
use App\Serializer\Normalizer\UserNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/news", name="news")
 */
class NewsController extends AbstractController
{
    /**
     * Получить новости
     * @Route("/", name="list", methods={"GET"})
     * @param Request $request,
     * @param NewsService $newsService
     * @param NewsFilterValidator $newsFilterValidator
     * @return Response
     */
    public function list(
        Request $request,
        NewsService $newsService,
        NewsFilterValidator $newsFilterValidator
    ): Response
    {
        /**
         * @var array
         */
        $content = json_decode($request->getContent(), true) ?? [];
        try {
            $newsFilterValidator->validation($content);
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
            'list' => $newsService->get($pg, $on, $dateFilter, $tagIds)
        ]);
    }
    
    /**
     * Получить новость
     * @Route("/{news<\d+>}", name="item", methods={"GET"})
     * @param News $news
     * @return Response
     */
    public function item(
        ?News $news,
        NewsNormalizer $newsNormalizer
    ): Response
    {
        if (!$news) {
            return $this->json([
                'message' => 'Новость не найдена'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($newsNormalizer->normalize($news));
    }

    /**
     * Поставить лайк
     * @IsGranted("ROLE_USER")
     * @Route("/like/{news<\d+>}", name="like", methods={"POST"})
     * @param News $news
     * @return Response
     */
    public function like(
        ?News $news,
        NewsService $newsService
    ): Response
    {
        if (!$news) {
            return $this->json([
                'message' => 'Новость не найдена'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $newsService->setLike(
                $news,
                $this->getUser()
            );
        } catch(Exception $e) {
            return $this->json([
                "error" => $e->getMessage()
            ], $e->getCode());
        }

        return $this->json([
            'message' => 'Лайк успешно поставлен'
        ], Response::HTTP_CREATED);
    }

    /**
     * Удалить лайк
     * @IsGranted("ROLE_USER")
     * @Route("/like/{news<\d+>}", name="dislike", methods={"DELETE"})
     * @param News $news
     * @return Response
     */
    public function dislike(
        ?News $news,
        NewsService $newsService
    ): Response
    {
        if (!$news) {
            return $this->json([
                'message' => 'Новость не найдена'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($newsService->removeLike(
            $news,
            $this->getUser()
        ), Response::HTTP_NO_CONTENT);
    }

     /**
     * Пользователи, которым понравилась новость
     * @Route("/likes/{news<\d+>}", name="likes", methods={"GET"})
     * @param News $news
     * @return Response
     */
    public function likes(
        ?News $news,
        UserNormalizer $userNormalizer
    ): Response
    {
        if (!$news) {
            return $this->json([
                'message' => 'Новость не найдена'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'list' => array_map(
                fn (User $user) => $userNormalizer->normalize($user),
                $news->getLikes()->getValues()
            )
        ]);
    }
}
