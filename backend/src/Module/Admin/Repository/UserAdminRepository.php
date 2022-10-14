<?php

namespace App\Module\Admin\Repository;

use App\Module\Admin\Entity\UserAdmin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAdmin|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAdmin|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAdmin[]    findAll()
 * @method UserAdmin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAdmin::class);
    }

    public function add(UserAdmin $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(UserAdmin $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

}
