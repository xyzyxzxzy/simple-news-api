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

#[Route(path: '/news', name: 'news')]
class NewsController extends AbstractController
{
    #[Route(path: '/', name: 'list', methods: ['GET'])]
    public function list(
        Request $request,
        NewsService $newsService,
        NewsFilterValidator $newsFilterValidator
    ): Response
    {
        $content = json_decode($request->getContent(), true) ?? [];
        try {
            $newsFilterValidator->validation($content);
        } catch(Exception $e) {
            return $this->json([
                "error" => unserialize($e->getMessage())
            ], $e->getCode());
        }

        $pg = $content['pg'] ?? $this->getParameter('app.pg');
        $on = $content['on'] ?? $this->getParameter('app.on');
        $dateFilter = $content['dateFilter'] ?? null;
        $tagIds = $content['tagIds'] ?? [];

        return $this->json([
            'list' => $newsService->get($pg, $on, $dateFilter, $tagIds)
        ]);
    }

    #[Route(path: '/{news<\d+>}', name: 'item', methods: ['GET'])]
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

    #[Route(path: '/like/{news<\d+>}', name: 'like', methods: ['POST'])]
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

    #[Route(path: '/like/{news<\d+>}', name: 'dislike', methods: ['DELETE'])]
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

    #[Route(path: '/likes/{news<\d+>}', name: 'likes', methods: ['GET'])]
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
