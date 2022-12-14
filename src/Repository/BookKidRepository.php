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
     *Find all Book_kid by Kid and category
     * 
     * @param int $kid_id
     * @param int $category_id
     * @return mixed
     */
    public function findAllByKidAndCategory($kid_id,$category_id) 
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
          
            ->setParameters(array('kid_id'=> $kid_id, 'category_id' => $category_id));

            return $query->getResult();      
   }

   /**
    * Find all books of a kid : by read or not read
    *
    * @param boolean $is_read_value 
    * @param int $kid_id
    * @return  mixed
    */
   public function findAllByIsRead($is_read_value, $kid_id)//:BookKid
   {
            $entityManager = $this->getEntityManager();
            $query = $entityManager->createQuery(
                
                'SELECT bk, k
                FROM App\Entity\BookKid bk
                JOIN bk.kid k
                WHERE k.id = :kid_id AND bk.is_read = :is_read_value
                '
            )
            ->setParameters(array('kid_id'=> $kid_id, 'is_read_value' => $is_read_value));

            return $query->getResult();
   }

   
   /**
       * Find One book_kid by: kid and book 
       *
       * @param int $book_id
       * @param int $kid_id
       * @return  mixed
       * 
       */
       public function findOneByKidandBook($kid_id, $book_id)
       {
                $entityManager = $this->getEntityManager();
                $query = $entityManager->createQuery(
                    
                    'SELECT bk, k
                    FROM App\Entity\BookKid bk
                    JOIN bk.kid k
                    JOIN bk.book o
                    WHERE k.id = :kid_id AND o.id = :book_id
                    '
                )

                ->setParameters(array('kid_id'=> $kid_id, 'book_id' => $book_id));
    

                return $query->getResult();
       }

       /**
       * Find Authors by kid 
       *
       * @return  mixed
       * 
       */
      public function findByAuthors($kid_id)
      {
        // $kid_id = 126;

        $entityManager = $this->getEntityManager();
       
        $qb = $entityManager->createQueryBuilder();
        
        $qb      
            ->select('bk')
            ->from('App\Entity\BookKid', 'bk')
            ->join('bk.book', 'b')
            ->join('b.authors', 'a') // join collection on the entity (not the property on the table) because of many to many
            ->addSelect('a.id')
            ->addSelect('a.name')
            ->where('bk.kid = :kid_id')
            ->setParameter('kid_id', $kid_id)
            ->groupBy('a.name');

        $result = $qb->getQuery()->getResult();


        return $result;
      }

      

       /**
       * Find Series by kid 
       *
       * @return  mixed
       * 
       */
      public function findBySeries($kid_id)
      {
        // $kid_id = 70;

        $entityManager = $this->getEntityManager();
       
        $qb = $entityManager->createQueryBuilder();
        
        $qb      
            ->select('bk')
            ->from('App\Entity\BookKid', 'bk')
            ->join('bk.series', 's') // join collection on the entity (not the property on the table) because of many to many
            ->addSelect('s.id')
            ->addSelect('s.name')
            ->where('bk.kid = :kid_id')
            ->setParameter('kid_id', $kid_id)
            ->groupBy('s.name');

        $result = $qb->getQuery()->getResult();


        return $result;
      }

      

       /**
       * Find all book_kid by: kid and serie 
       *
       * @param int $serie_id
       * @param int $kid_id
       * @return  mixed
       * 
       */
      public function findAllByKidAndSerie($kid_id, $serie_id)
      {
               $entityManager = $this->getEntityManager();
               $query = $entityManager->createQuery(
                   
                   'SELECT bk, k
                   FROM App\Entity\BookKid bk
                   JOIN bk.kid k
                   JOIN bk.series s
                   WHERE k.id = :kid_id AND s.id = :series_id
                   '
               )

               ->setParameters(array('kid_id'=> $kid_id, 'series_id' => $serie_id));
   

               return $query->getResult();
      }
}
