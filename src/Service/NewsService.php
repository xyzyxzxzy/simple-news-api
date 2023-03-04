<?php

namespace App\Service;

use App\Form\Model\News\NewsCreateModel;
use App\Form\Model\News\NewsUpdateModel;
use claviska\SimpleImage;
use Exception;
use App\Entity\News;
use App\Entity\User;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Serializer\Normalizer\NewsNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class NewsService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SluggerInterface $slugger,
        private readonly ParameterBagInterface $parameterBag,
        private readonly NewsRepository $newsRepository,
        private readonly NewsNormalizer $newsNormalizer,
        private readonly UtilsService $utilsService
    ) {}

    public function get(int $pg, int $on, ?array $tagIds, ?string $dateFilter): array
    {
        return array_map(function (News $news) {
            return $this->newsNormalizer->normalize($news);
        }, $this->newsRepository->getNews($pg, $on, $tagIds, $dateFilter));
    }

    public function create(NewsCreateModel $data, User $user): int
    {
        $news = new News($data, $user);
        $news->addTags($data->tags);

        $this->em->persist($news);
        $this->em->flush();

        $news->setSlug($this->slugger->slug($data->name . '-' . $news->getId())->lower())
            ->setPreview($this->uploadPreview($news->getId(), $data->preview));

        $this->em->flush();

        return $news->getId();
    }

    public function update(News $news, NewsUpdateModel $data): int
    {
        if ($data->name) {
            $news->setName($data->name)
                ->setSlug($this->slugger->slug($data->name . '-' . $news->getId())->lower());
        }

        if ($data->content) {
            $news->setContent($data->content);
        }

        if ($data->tags) {
            $news->addTags($data->tags);
        }

        if ($data->preview) {
            $news->setPreview($this->uploadPreview($news->getId(), $data->preview));
        }

        $this->em->flush();

        return $news->getId();
    }

    public function delete(News $news): void
    {
        $rootDir = $this->parameterBag->get('kernel.project_dir');

        try {
            $this->utilsService->recursiveRemoveDir($rootDir . News::PATH_TO_SAVE . $news->getId());
            $this->em->remove($news);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $this->em->flush();
    }

    private function uploadPreview(int $newsId, string $imagePath): string
    {
        $rootDir = $this->parameterBag->get('kernel.project_dir');
        $pathToSave = News::PATH_TO_SAVE . $newsId . '/';

        if (!file_exists($rootDir . $pathToSave)) {
            mkdir($rootDir . $pathToSave, 0777, true);
        }

        $image = new SimpleImage($imagePath);
        $image
            ->resize(News::PREVIEW_WIDTH, News::PREVIEW_HEIGHT)
            ->toFile($rootDir . $pathToSave . 'preview.jpg');
        unlink($imagePath);

        return $pathToSave . 'preview.jpg';
    }

    public function setLike(News $news, User $user): void
    {
        $likedNews = $this->em->getRepository(News::class)->getLike($news, $user);

        if (!is_null($likedNews)) {
            throw new Exception("You have already liked", Response::HTTP_BAD_REQUEST);
        }

        $news->addLike($user);
        $this->em->flush();
    }

    public function removeLike(News $news, User $user): void
    {
        $likedNews = $this->em->getRepository(News::class)->getLike($news, $user);
        
        if (!is_null($likedNews)) {
            $likedNews->removeLike($user);

            $this->em->persist($likedNews);
            $this->em->flush();
        }
    }
}
