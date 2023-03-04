<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\User;
use App\Form\News\NewsFilterForm;
use App\Form\Type\NewsListType;
use App\Form\Type\Payment\MainOfferPaymentForm;
use App\Serializer\Normalizer\NewsNormalizer;
use App\Serializer\Normalizer\UserNormalizer;
use App\Service\FormErrorsHelper;
use App\Service\NewsService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/news', name: 'news')]
class NewsController extends AbstractController
{
    public function __construct(
        private readonly FormErrorsHelper $formErrorsHelper,
        private readonly NewsService $newsService,
        private readonly NewsNormalizer $newsNormalizer,
    ) {}

    #[Route(path: '/', name: 'list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $form = $this->createForm(NewsFilterForm::class, options: ['method' => $request->getMethod()])
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

        return $this->json(['list' => $this->newsService->get($data->pg, $data->on, $data->tagIds, $data->dateFilter)]);
    }

    #[Route(path: '/{news<\d+>}', requirements: ['news' => '\d'] , name: 'item', methods: ['GET'])]
    public function item(?News $news): Response
    {
        if (!$news) {
            return $this->json([
                'message' => 'News not found'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($this->newsNormalizer->normalize($news));
    }

    #[Route(path: '/like/{news<\d+>}', requirements: ['news' => '\d'], name: 'like', methods: ['POST'])]
    public function like(?News $news): Response
    {
        if (!$news) {
            return $this->json([
                'message' => 'News not found'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->newsService->setLike($news, $this->getUser());
        } catch(Exception $e) {
            return $this->json([
                "error" => $e->getMessage()
            ], $e->getCode());
        }

        return $this->json(['message' => 'Like successfully placed'], Response::HTTP_CREATED);
    }

    #[Route(path: '/like/{news<\d+>}', requirements: ['news' => '\d'], name: 'dislike', methods: ['DELETE'])]
    public function dislike(?News $news): Response
    {
        if (!$news) {
            return $this->json(['message' => 'News not found'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($this->newsService->removeLike($news, $this->getUser()), Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/likes/{news<\d+>}', requirements: ['news' => '\d'], name: 'likes', methods: ['GET'])]
    public function likes(?News $news): Response
    {
        if (!$news) {
            return $this->json(['message' => 'News not found'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['list' => array_map(
                fn (User $user) => $this->userNormalizer->normalize($user),
                $news->getLikes()->getValues()
            )
        ]);
    }
}
