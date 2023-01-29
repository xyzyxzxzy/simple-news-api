<?php

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Serializer\Normalizer\TagNormalizer;
use Doctrine\ORM\EntityManagerInterface;

class TagService 
{
    private $em;
    private $tagRepository;
    private $tagNormalizer;
    private $utilsService;

    public function __construct(
        EntityManagerInterface $em,
        TagRepository $tagRepository,
        TagNormalizer $tagNormalizer,
        UtilsService $utilsService
    ) 
    {
        $this->em = $em;
        $this->tagRepository = $tagRepository;
        $this->tagNormalizer = $tagNormalizer;
        $this->utilsService = $utilsService;
    }

    public function get(
        int $pg,
        int $on
    ): array {
        return array_map(function(Tag $tag) {
            return $this->tagNormalizer->normalize($tag);
        }, $this->tagRepository->getTags($pg, $on));
    }

    public function create(
        array $data
    ): int {
        $name = $this->utilsService->convertString($data['name']);

        $tag = new Tag;
        $tag->setName($name);

        $this->em->persist($tag);
        $this->em->flush();

        return $tag->getId();
    }

    public function update(
        Tag $tag,
        array $data
    ): int {
        $name = $this->utilsService->convertString($data['name']);

        if (strlen($name) > 0) {
            $tag->setName($name);
        }

        $this->em->flush();

        return $tag->getId();
    }

    public function delete(
        Tag $tag
    ): void {
        $this->em->remove($tag);
        $this->em->flush();
    }
}