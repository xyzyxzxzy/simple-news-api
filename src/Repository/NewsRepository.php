<?php

namespace App\Repository;

use App\Entity\News;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 *
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(News $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(News $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Получить список новостей
     * @param int $pg
     * @param int $on
     * @param string|null $dataFilter
     * @param array $tagIds
     * @return array
     */
    public function getNews(int $pg, int $on, string $dataFilter = null, array $tagIds): array
    {
        $query = $this->createQueryBuilder('n')
            ->setFirstResult($on * ($pg - 1))
            ->setMaxResults($on)
            ->orderBy('n.datePublication', 'ASC');
        
        if ($dataFilter) {
            $dataFilter = new DateTime($dataFilter);
            $query
                ->where("DATE_FORMAT(n.datePublication, '%m-%Y') = :dataFilter")
                ->setParameter('dataFilter', $dataFilter->format('m-Y'));
        }

        if (count($tagIds) > 0) {
            $query
                ->innerJoin('n.tag', 't')
                ->andWhere('t.id IN (:tagIds)')
                ->setParameter('tagIds', $tagIds);
        }

        return $query->getQuery()->getResult();
    }
    
    /**
     * Поставлен ли лайк пользователем для текущей новости
     * @param News $news
     * @param User $user
     * @return News|null
     */
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
