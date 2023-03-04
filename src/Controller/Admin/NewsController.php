<?php

namespace App\Controller\Admin;

use App\Form\News\NewsCreateForm;
use App\Form\News\NewsUpdateForm;
use App\Service\FormErrorsHelper;
use App\Entity\News;
use App\Service\NewsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/admin/news', name: 'news')]
class NewsController extends AbstractController
{
    public function __construct(
        private readonly FormErrorsHelper $formErrorsHelper,
        private readonly NewsService $newsService,
    ) {}

    #[Route(path: '/', name: 'create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(
            NewsCreateForm::class,
            options: ['method' => $request->getMethod()]
        )
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

        return $this->json(['id' => $this->newsService->create($data, $this->getUser())], Response::HTTP_CREATED);
    }

    #[Route(path: '/{news<\d+>}', name: 'update', methods: ['PATCH'])]
    public function update(?News $news, Request $request): Response
    {
        if (!$news) {
            return $this->json([
                'message' => 'News not found'
            ], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(
            NewsUpdateForm::class,
            options: ['method' => $request->getMethod()]
        )
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

        return $this->json(['id' => $this->newsService->update($news, $data)], Response::HTTP_OK);
    }

    #[Route(path: '/{news<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(?News $news): Response
    {
        if (!$news) {
            return $this->json([
                'message' => 'Новость не найдена'
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->newsService->delete($news);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
