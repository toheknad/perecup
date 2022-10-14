<?php

namespace App\Module\UrlChecked\Repository;


use App\Module\UrlChecked\Entity\UrlChecked;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class UrlCheckedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UrlChecked::class);
    }


    /**
     * Сбрасывает отработанные прокси на новый круг
     * @return void
     */
    private function resetStatusProxy()
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->update()
            ->set('p.status', true)
            ->where('p.status=false');
        $qb->getQuery()->execute();
    }
}
