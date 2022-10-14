<?php

namespace App\Module\Proxy\Repository;


use App\Module\Proxy\Entity\Proxy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;


class ProxyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proxy::class);
    }


    /**
     * @return Proxy|null
     */
    public function getNextProxy(): ?Proxy
    {
        $proxy = $this->findOneBy(['status' => 1]);
        if (is_null($proxy)) {
            $this->resetStatusProxy();
            $proxy = $this->findOneBy(['status' => 1]);
        }

        if (!is_null($proxy)) {
            $proxy->setStatus(false);
            $this->getEntityManager()->flush($proxy);
        }
        return $proxy;
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
