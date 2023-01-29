<?php

namespace App\Controller;

use Exception;
use App\Entity\Tag;
use App\Service\TagService;
use App\Validator\TagFilterValidator;
use App\Serializer\Normalizer\TagNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/tag', name: 'tag')]
class TagController extends AbstractController
{
    #[Route(path: '/', name: 'list', methods: ['GET'])]
    public function list(
        Request $request,
        TagService $tagService,
        TagFilterValidator $tagFilterValidator
    ): Response
    {
        $content = json_decode($request->getContent(), true) ?? [];
        try {
            $tagFilterValidator->validation($content);
        } catch(Exception $e) {
            return $this->json([
                "error" => unserialize($e->getMessage())
            ], $e->getCode());
        }

        $pg = $content['pg'] ?? $this->getParameter('app.pg');
        $on = $content['on'] ?? $this->getParameter('app.on');

        return $this->json([
            'list' => $tagService->get($pg, $on)
        ]);
    }

    #[Route(path: '/{tag<\d+>}', name: 'item', methods: ['GET'])]
    public function item(
        ?Tag $tag,
        TagNormalizer $tagNormalizer
    ): Response
    {
        if (!$tag) {
            return $this->json([
                'message' => 'Тег не найден'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($tagNormalizer->normalize($tag));
    }
}
