<?php

namespace App\Controller;

use Exception;
use App\Entity\Tag;
use App\Service\TagService;
use App\Service\ValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tag", name="tag")
 */
class TagController extends AbstractController
{
    /**
     * Получить теги
     * @Route("/", name="tags", methods={"GET"})
     * @param Request $request,
     * @param TagService $tagService
     * @param ValidationService $validationService
     * @return Response
     */
    public function tags(
        Request $request,
        TagService $tagService,
        ValidationService $validationService
    ): Response
    {
        /**
         * @var array
         */
        $content = json_decode($request->getContent(), true);
        try {
            $validationService->requestValidation($content, $tagService->getConstraints());
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

        return $this->json([
            'list' => $tagService->getTags($pg, $on)
        ]);
    }
    
    /**
     * Получить тег
     * @Route("/{tag<\d+>}", name="one", methods={"GET"})
     * @param Tag $tag
     * @param TagService $tagService
     * @return Response
     */
    public function one(
        ?Tag $tag,
        TagService $tagService
    ): Response
    {
        if (!$tag) {
            return $this->json([
                'message' => 'Тег не найден'
            ], 400);
        }

        return $this->json($tagService->tagNormalizer($tag));
    }
}
