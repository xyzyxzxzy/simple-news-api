<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Form\Tag\TagUpdateForm;
use App\Service\TagService;
use App\Form\Tag\TagCreateForm;
use App\Service\FormErrorsHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/admin/tag', name: 'tag')]
class TagController extends AbstractController
{
    public function __construct(
        private readonly FormErrorsHelper $formErrorsHelper,
        private readonly TagService $tagService,
    ) {}

    #[Route(path: '/', name: 'create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(TagCreateForm::class, options: ['method' => $request->getMethod()])
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                return $this->json(
                    ['errors' => $this->formErrorsHelper->prepareErrors($form)],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        $data = $form->getData();

        return $this->json(['id' => $this->tagService->create($data)], Response::HTTP_CREATED);
    }

    #[Route(path: '/{tag<\d+>}', name: 'update', methods: ['PATCH'])]
    public function update(?Tag $tag, Request $request): Response
    {
        if (!$tag) {
            return $this->json([
                'message' => 'Тег не найден'
            ], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(TagUpdateForm::class, options: ['method' => $request->getMethod()])
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                return $this->json(
                    ['errors' => $this->formErrorsHelper->prepareErrors($form)],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        $data = $form->getData();

        return $this->json(['id' => $this->tagService->update($tag, $data)], Response::HTTP_OK);
    }

    #[Route(path: '/{tag<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(?Tag $tag,): Response
    {
        if (!$tag) {
            return $this->json([
                'message' => 'Тег не найден'
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->tagService->delete($tag);

        return $this->json('', Response::HTTP_NO_CONTENT);
    }
}
