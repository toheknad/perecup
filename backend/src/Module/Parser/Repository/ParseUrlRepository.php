<?php

namespace App\Module\Parser\Repository;

use App\Module\Parser\Entity\ParseUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

/**
 * @method ParseUrl|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParseUrl|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParseUrl[]    findAll()
 * @method ParseUrl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParseUrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParseUrl::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ParseUrl $entity, bool $flush = true): void
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
    public function remove(ParseUrl $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Возвращает Url-ы для парсинга урлов объявлений, активные на сейчас
     * с учётом поля period
     * @return array
     */
    public function getActiveNow(): array
    {
        $l = $this->findBy(['isActive' => true]);

        $res = [];

        $m = (int)(new DateTime())->format('i');
        foreach ($l as $item) {
            if (!($m % $item->getPeriod())) {
                $res[] = $item;
            }
        }
        return $res;
    }

    public function getUrlsForProxy(int $lastUrlId, int $amount)
    {
        $qb = $this->createQueryBuilder('p')
            ->setFirstResult($lastUrlId)
            ->setMaxResults($amount)
            ->orderBy('p.id', 'ASC');

        return $qb->getQuery()->execute();
    }

}
