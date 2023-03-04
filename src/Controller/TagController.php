<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\Common\MainFilterForm;
use App\Form\Model\Tag\TagFilterModel;
use App\Serializer\Normalizer\TagNormalizer;
use App\Service\FormErrorsHelper;
use App\Service\TagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/tag', name: 'tag')]
class TagController extends AbstractController
{
    public function __construct(
        private readonly TagService $tagService,
        private readonly TagNormalizer $tagNormalizer,
        private readonly FormErrorsHelper $formErrorsHelper,
    ) {}

    #[Route(path: '/', name: 'list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $form = $this->createForm(MainFilterForm::class, new TagFilterModel(), options: ['method' => $request->getMethod()])
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

        return $this->json(['list' => $this->tagService->get($data->pg, $data->on)]);
    }

    #[Route(path: '/{tag<\d+>}', name: 'item', requirements: ['tag' => '\d'], methods: ['GET'])]
    public function item(?Tag $tag): Response
    {
        if (!$tag) {
            return $this->json(['message' => 'Tag not found'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($this->tagNormalizer->normalize($tag));
    }
}
