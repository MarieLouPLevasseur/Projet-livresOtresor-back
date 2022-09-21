<?php

namespace App\Repository;

use App\Entity\Avatar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Avatar>
 *
 * @method Avatar|null find($id, $lockMode = null, $lockVersion = null)
 * @method Avatar|null findOneBy(array $criteria, array $orderBy = null)
 * @method Avatar[]    findAll()
 * @method Avatar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvatarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avatar::class);
    }

    public function add(Avatar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Avatar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * Find Only an avatar by a specific Win value
     *
     * @param [type] $value
     * @return mixed
     */
    public function findOneByIsWinValue($value): ?Avatar
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.is_win <= :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    /**
     * Find All avatars by is Win value
     *
     * @param int $value Is_win Value
     * @return mixed
     */
    public function findAllByIsWinValue($value)
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.is_win <= :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getResult()
       ;
   }
}
