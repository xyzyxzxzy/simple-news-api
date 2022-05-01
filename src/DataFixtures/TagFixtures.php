<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    const TAGS = [
        1 => 'Экономика',
        2 => 'Политика',
        3 => 'Общество',
        4 => 'Россия',
        5 => 'Путин',
    ];

    /**
     * Фикстуры тегов
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::TAGS as $tag) {
            $newTag = new Tag();
            $newTag->setName($tag);
            $manager->persist($newTag);
        }
        
        $manager->flush();
    }
}
