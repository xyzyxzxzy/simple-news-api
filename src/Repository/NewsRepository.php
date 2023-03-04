<?php

namespace App\Repository;

use App\Entity\News;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function add(News $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(News $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getNews(int $pg, int $on, ?array $tagIds, ?string $dataFilter): array
    {
        $query = $this->createQueryBuilder('n')
            ->setFirstResult($on * ($pg - 1))
            ->setMaxResults($on)
            ->orderBy('n.datePublication', 'ASC');

        if ($dataFilter) {
            $dataFilter = new DateTime($dataFilter);

            $query
                ->where("TO_CHAR(n.datePublication, 'mm-YYYY') = :dataFilter")
                ->setParameter('dataFilter', $dataFilter->format('m-Y'));
        }

        if ($tagIds) {
            $query
                ->innerJoin('n.tag', 't')
                ->andWhere('t.id IN (:tagIds)')
                ->setParameter('tagIds', $tagIds);
        }

        return $query->getQuery()->getResult();
    }

    public function getLike(News $news, User $user)
    {
        $query = $this->createQueryBuilder('n')
            ->innerJoin('n.likes', 'l')
            ->where('l.id = :userId')
            ->andWhere('n.id = :newsId')
            ->setParameters([
                'newsId' => $news->getId(),
                'userId' => $user->getId()
            ]);

        return $query->getQuery()->getOneOrNullResult();
    }
}
