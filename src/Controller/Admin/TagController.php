<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\Tag;
use App\Service\TagService;
use App\Validator\TagValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/tag", name="tag")
 */
class TagController extends AbstractController
{
    /**
     * Добавить тег
     * @Route("/", name="create", methods={"POST"})
     * @param Request $request,
     * @param TagService $tagService
     * @param TagValidator $tagValidator
     * @return Response
     */
    public function create(
        Request $request,
        TagService $tagService,
        TagValidator $tagValidator
    ): Response
    {
        /**
         * @var array
         */
        $content = json_decode($request->getContent(), true) ?? [];
        try {
            $tagValidator->validation($content);
        } catch(Exception $e) {
            return $this->json([
                "error" => unserialize($e->getMessage())
            ], $e->getCode());
        }

        return $this->json([
            'id' => $tagService->create($content)
        ], Response::HTTP_CREATED);
    }

    /**
     * Редактировать тег
     * @Route("/{tag<\d+>}", name="update", methods={"PATCH"})
     * @param Tag $tag,
     * @param Request $request,
     * @param TagService $tagService
     * @param TagValidator $tagValidator
     * @return Response
     */
    public function update(
        ?Tag $tag,
        Request $request,
        TagService $tagService,
        TagValidator $tagValidator
    ): Response
    {
        if (!$tag) {
            return $this->json([
                'message' => 'Тег не найден'
            ], Response::HTTP_BAD_REQUEST);
        }

        /**
         * @var array
         */
        $content = json_decode($request->getContent(), true) ?? [];
        try {
            $tagValidator->validation($content);
        } catch(Exception $e) {
            return $this->json([
                "error" => unserialize($e->getMessage())
            ], $e->getCode());
        }

        return $this->json([
            'id' => $tagService->update(
                $tag,
                $content
            )
        ], Response::HTTP_OK);
    }
    
    /**
     * Удалить тег
     * @Route("/{tag<\d+>}", name="delete", methods={"DELETE"})
     * @param Tag $tag
     * @param TagService $tagService
     * @return Response
     */
    public function delete(
        ?Tag $tag,
        TagService $tagService
    ): Response
    {
        if (!$tag) {
            return $this->json([
                'message' => 'Тег не найден'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        return $this->json(
            $tagService->delete($tag),
            Response::HTTP_NO_CONTENT
        );
    }
}
