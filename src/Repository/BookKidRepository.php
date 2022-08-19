<?php

namespace App\Repository;

use App\Entity\BookKid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookKid>
 *
 * @method BookKid|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookKid|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookKid[]    findAll()
 * @method BookKid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookKidRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookKid::class);
    }

    public function add(BookKid $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BookKid $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * 
     *

     * @param [type] $kid_id
     * @param [type] $category_id
     * 
     */
    public function findAllByKidAndCategory($kid_id,$category_id) //: ?BookKid
   {

            $entityManager = $this->getEntityManager();
            $query = $entityManager->createQuery(
                // ! les alias sont obligatoire ici
               'SELECT bk, b, k, c
                FROM App\Entity\BookKid bk
                JOIN bk.kid k
                JOIN bk.book b
                JOIN bk.category c
                WHERE k.id = :kid_id AND c.id = :category_id
                '
            )
            // ->setParameter('kid_id', $kid_id, 'category_id', $category_id);
            ->setParameters(array('kid_id'=> $kid_id, 'category_id' => $category_id));

            // returns an array of Product objects
            return $query->getResult();

      
   }

   /**
    * Find all book of a kid : by read or not read
    *
    * @param boolean $is_read_value
    * @param int $kid_id
    * 
    */
   public function findAllByIsRead($is_read_value, $kid_id)//:BookKid
   {
            $entityManager = $this->getEntityManager();
            $query = $entityManager->createQuery(
                // ! les alias sont obligatoire ici
                'SELECT bk, k
                FROM App\Entity\BookKid bk
                JOIN bk.kid k
                WHERE k.id = :kid_id AND bk.is_read = :is_read_value
                '
            )
            // ->setParameter('kid_id', $kid_id, 'category_id', $category_id);
            ->setParameters(array('kid_id'=> $kid_id, 'is_read_value' => $is_read_value));

            // returns an array of Product objects
            return $query->getResult();

   }

//    /**
//     * @return BookKid[] Returns an array of BookKid objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BookKid
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
