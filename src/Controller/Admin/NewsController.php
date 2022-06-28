<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\News;
use App\Service\NewsService;
use App\Validator\NewsValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/news", name="news")
 */
class NewsController extends AbstractController
{
    /**
     * Добавить новость
     * @Route("/", name="create", methods={"POST"})
     * @param Request $request,
     * @param NewsService $newsService
     * @param NewsValidator $newsValidator
     * @return Response
     */
    public function create(
        Request $request,
        NewsService $newsService,
        NewsValidator $newsValidator
    ): Response
    {
        /**
         * @var array
         */
        $content = json_decode($request->getContent(), true) ?? [];
        try {
            $newsValidator->validation($content);
        } catch(Exception $e) {
            return $this->json([
                "error" => unserialize($e->getMessage())
            ], $e->getCode());
        }

        $content += [
            'user' => $this->getUser()
        ];

        return $this->json([
            'id' => $newsService->create($content)
        ], Response::HTTP_CREATED);
    }

    /**
     * Редактировать новость
     * @Route("/{news<\d+>}", name="update", methods={"PATCH"})
     * @param News $news,
     * @param Request $request,
     * @param NewsService $newsService
     * @param NewsValidator $newsValidator
     * @return Response
     */
    public function update(
        ?News $news,
        Request $request,
        NewsService $newsService,
        NewsValidator $newsValidator
    ): Response
    {
        if (!$news) {
            return $this->json([
                'message' => 'Новость не найдена'
            ], Response::HTTP_BAD_REQUEST);
        }

        /**
         * @var array
         */
        $content = json_decode($request->getContent(), true) ?? [];
        try {
            $newsValidator->validation($content, [
                'allowMissingFields' => true
            ]);
        } catch(Exception $e) {
            return $this->json([
                "error" => unserialize($e->getMessage())
            ], $e->getCode());
        }

        return $this->json([
            'id' => $newsService->update(
                $news,
                $content
            )
        ], Response::HTTP_OK);
    }
    
    /**
     * Удалить новость
     * @Route("/{news<\d+>}", name="delete", methods={"DELETE"})
     * @param News $news
     * @param NewsService $newsService
     * @return Response
     */
    public function delete(
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
            $newsService->delete($news);
        } catch(Exception $e) {
            return $this->json([
                "error" => $e->getMessage()
            ], $e->getCode());
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
