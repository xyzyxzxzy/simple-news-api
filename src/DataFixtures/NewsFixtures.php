<?php

namespace App\DataFixtures;

use claviska\SimpleImage;
use DateTime;
use App\Entity\News;
use App\Entity\User;
use App\Repository\TagRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class NewsFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugger;
    protected $parameterBag;
    private $tagRepository;

    public function __construct(
        SluggerInterface $slugger,
        ParameterBagInterface $parameterBag,
        TagRepository $tagRepository,
    )
    {
        $this->slugger = $slugger;
        $this->parameterBag = $parameterBag;
        $this->tagRepository = $tagRepository;
    }
    
    const NEWS = [
        1 => [
            'title' => 'Новость 1',
            'content' => 'test test'
        ],
        2 => [
            'title' => 'Новость 2',
            'content' => 'test test'
        ],
        3 => [
            'title' => 'Новость 3',
            'content' => 'test test'
        ],
        4 => [
            'title' => 'Новость 4',
            'content' => 'test test'
        ],
        5 => [
            'title' => 'Новость 5',
            'content' => 'test test'
        ],
        6 => [
            'title' => 'Новость 6',
            'content' => 'test test'
        ],
        7 => [
            'title' => 'Новость 7',
            'content' => 'test test'
        ],
        8 => [
            'title' => 'Новость 8',
            'content' => 'test test'
        ],
        9 => [
            'title' => 'Новость 9',
            'content' => 'test test'
        ],
        10 => [
            'title' => 'Новость 10',
            'content' => 'test test'
        ],
    ];

    /**
     * Фикстуры новостей
     * @return void 
     */
    public function load(
        ObjectManager $manager
    ): void
    {   
        $tags = $this->tagRepository->findAll();

        foreach (self::NEWS as $index => $news) {
            $newNews = new News;
            $newNews->setName($news['title']);
            $newNews->setSlug($this->slugger->slug($news['title'])->lower());
            $newNews->setContent($news['content'] . ' ' . $index);
            $newNews->setDatePublication(new DateTime('now'));
            $newNews->setAuthor($manager
                ->getRepository(User::class)
                ->findOneBy([
                    'email' => 'admin'
                ])
            );
            
            foreach ($tags as $tag) {
                $newNews->addTag($tag);
            }

            $manager->persist($newNews);
            $manager->flush();
            $newNews->setPreview($this->uploadPreview($newNews->getId()));
        }
        
        $manager->flush();
    }

    /**
     * Загружаем превью
     * @var int $newsId
     * @return string
     */
    private function uploadPreview(
        int $newsId
    ): string {
        $rootDir = $this->parameterBag->get('kernel.project_dir');
        $pathToSave = News::PATH_TO_SAVE . $newsId . '/';
        
        if (!file_exists($rootDir . $pathToSave)) {
            mkdir($rootDir . $pathToSave, 0777, true);
        }

        $image = new SimpleImage('https://source.unsplash.com/collection/928423/400x400');
        $image
            ->resize(News::PREVIEW_WIDTH, News::PREVIEW_HEIGHT)
            ->toFile($rootDir . $pathToSave . 'preview.jpg');
        
        return $pathToSave . 'preview.jpg';
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TagFixtures::class
        ];
    }
}
