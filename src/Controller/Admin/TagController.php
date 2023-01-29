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

#[Route(path: '/admin/tag', name: 'tag')]
class TagController extends AbstractController
{
    #[Route(path: '/', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        TagService $tagService,
        TagValidator $tagValidator
    ): Response
    {
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

    #[Route(path: '/{tag<\d+>}', name: 'update', methods: ['PATCH'])]
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

    #[Route(path: '/{tag<\d+>}', name: 'delete', methods: ['DELETE'])]
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

        $tagService->delete($tag);

        return $this->json('', Response::HTTP_NO_CONTENT);
    }
}
