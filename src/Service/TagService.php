<?php

namespace App\Service;

use App\Entity\Tag;
use App\Form\Model\Tag\TagCreateModel;
use App\Form\Model\Tag\TagUpdateModel;
use App\Repository\TagRepository;
use App\Serializer\Normalizer\TagNormalizer;
use Doctrine\ORM\EntityManagerInterface;

class TagService 
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TagRepository $tagRepository,
        private readonly TagNormalizer $tagNormalizer,
    ) {}

    public function get(int $pg, int $on): array
    {
        return array_map(function(Tag $tag) {
            return $this->tagNormalizer->normalize($tag);
        }, $this->tagRepository->getTags($pg, $on));
    }

    public function create(TagCreateModel $data): int
    {
        $tag = new Tag($data->name);

        $this->em->persist($tag);
        $this->em->flush();

        return $tag->getId();
    }

    public function update(Tag $tag, TagUpdateModel $data): int
    {
        if ($data->name) {
            $tag->setName($data->name);
        }

        $this->em->flush();

        return $tag->getId();
    }

    public function delete(Tag $tag): void
    {
        $this->em->remove($tag);
        $this->em->flush();
    }
}