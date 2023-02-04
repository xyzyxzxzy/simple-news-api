<?php

namespace App\Controller;

use App\Form\Model\TagFilterModel;
use App\Form\Type\MainFilterTypeForm;
use App\Service\FormErrorsHelper;
use App\Entity\Tag;
use App\Service\TagService;
use App\Serializer\Normalizer\TagNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/tag', name: 'tag')]
class TagController extends AbstractController
{
    public function __construct(
        private readonly FormErrorsHelper $formErrorsHelper,
    ) {}

    #[Route(path: '/', name: 'list', methods: ['GET'])]
    public function list(Request $request, TagService $tagService): Response
    {
        $form = $this->createForm(MainFilterTypeForm::class, new TagFilterModel(), options: ['method' => $request->getMethod()])
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

        return $this->json(['list' => $tagService->get($data->pg, $data->on)]);
    }

    #[Route(path: '/{tag<\d+>}', name: 'item', requirements: ['tag' => '\d'], methods: ['GET'])]
    public function item(?Tag $tag, TagNormalizer $tagNormalizer): Response
    {
        if (!$tag) {
            return $this->json(['message' => 'Tag not found'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($tagNormalizer->normalize($tag));
    }
}
