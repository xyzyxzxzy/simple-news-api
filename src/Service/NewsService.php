<?php

namespace App\Service;

use Exception;
use App\Entity\Tag;
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
    private $em;
    private $slugger;
    private $parameterBag;
    private $newsRepository;
    private $newsNormalizer;
    private $utilsService;

    public function __construct(
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        ParameterBagInterface $parameterBag,
        NewsRepository $newsRepository,
        NewsNormalizer $newsNormalizer,
        UtilsService $utilsService
    ) {
        $this->em = $em;
        $this->slugger = $slugger;
        $this->parameterBag = $parameterBag;
        $this->newsRepository = $newsRepository;
        $this->newsNormalizer = $newsNormalizer;
        $this->utilsService = $utilsService;
    }

    /**
     * Получить новости
     * @param int $pg
     * @param int $on
     * @param string|null $dataFilter
     * @param array $tagIds
     * @return array
     */
    public function get(
        int $pg,
        int $on,
        string $dateFilter = null,
        array $tagIds
    ): array {
        return array_map(function (News $news) {
            return $this->newsNormalizer->normalize($news);
        }, $this->newsRepository->getNews($pg, $on, $dateFilter, $tagIds));
    }

    /**
     * Добавить новость
     * @param array $data
     * @return int
     */
    public function create(
        array $data
    ): int {
        /**
         * @var string
         */
        $name = $this->utilsService->convertString($data['name']);

        /**
         * @var string
         */
        $preview = $data['preview'];

        /**
         * @var string
         */
        $content = $this->utilsService->convertString($data['content']);

        /**
         * @var array
         */
        $tags = $data['tagIds'];

        /**
         * Автор новости
         * 
         * @var User
         */
        $user = $data['user'];

        $news = new News;
        $news->setName($name);
        $news->setContent($content);
        $news->setDatePublication(new \DateTime('now'));
        $news->setAuthor($user);

        foreach ($tags as $tag) {
            $news->addTag(
                $this->em
                    ->getRepository(Tag::class)
                    ->find($tag)
            );
        }

        $this->em->persist($news);
        $this->em->flush();
        $news->setSlug(
            $this->slugger
                ->slug($name . '-' . $news->getId())
                ->lower()
        );
        $news->setPreview(
            $this
                ->uploadPreview(
                    $news->getId(),
                    $preview
                )
        );
        $this->em->flush();

        return $news->getId();
    }

    /**
     * Редактировать новость
     * @param News $news
     * @param array $data
     * @return int
     */
    public function update(
        News $news,
        array $data
    ): int {
        /**
         * @var string
         */
        $name = $this->utilsService->convertString($data['name'] ?? '');

        /**
         * @var string
         */
        $preview = $data['preview'] ?? '';

        /**
         * @var string
         */
        $content = $this->utilsService->convertString($data['content'] ?? '');

        /**
         * @var array
         */
        $tags = $data['tagIds'] ?? [];

        if (strlen($name) > 0) {
            $news->setName($name);
            $news->setSlug(
                $this->slugger
                    ->slug($name . '-' . $news->getId())
                    ->lower()
            );
        }

        if (strlen($content) > 0) {
            $news->setContent($content);
        }

        if (!empty($tags)) {
            foreach ($news->getTag() as $tag) {
                $tag->removeNews($news);
            }

            foreach ($tags as $tag) {
                $news->addTag(
                    $this->em
                        ->getRepository(Tag::class)
                        ->find($tag)
                );
            }
        }

        if (strlen($preview) > 0) {
            $news->setPreview(
                $this->uploadPreview(
                    $news->getId(),
                    $preview
                )
            );
        }

        $this->em->flush();

        return $news->getId();
    }

    /**
     * Удалить новость
     * @param News $news
     * @return void
     */
    public function delete(
        News $news
    ): void {
        /**
         * @var string
         */
        $rootDir = $this->parameterBag->get('kernel.project_dir');

        try {
            $this->utilsService->recursiveRemoveDir($rootDir . News::PATH_TO_SAVE . $news->getId());
            $this->em->remove($news);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $this->em->flush();
    }

    /**
     * Загружаем превью
     * @var int $newsId
     * @return string
     */
    private function uploadPreview(
        int $newsId,
        string $imagePath
    ): string {
        $rootDir = $this->parameterBag->get('kernel.project_dir');
        $pathToSave = News::PATH_TO_SAVE . $newsId . '/';

        if (!file_exists($rootDir . $pathToSave)) {
            mkdir($rootDir . $pathToSave, 0777, true);
        }

        $image = new \abeautifulsite\SimpleImage($imagePath);
        $image
            ->resize(News::PREVIEW_WIDTH, News::PREVIEW_HEIGHT)
            ->save($rootDir . $pathToSave . 'preview.jpg', 'image/jpg');
        unlink($imagePath);

        return $pathToSave . 'preview.jpg';
    }
}
