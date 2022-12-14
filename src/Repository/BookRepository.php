<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function add(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * Find a book by its ISBN code
     *
     * @param int $isbn ISBN code 
     * @return Book|null
     */
    public function findOneByIsbnCode($isbn): ? Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.isbn = :val')
            ->setParameter('val', $isbn)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
      * Find/search movies by title/content
      *
      * @param string $query user search 

      * @return void
      */
      public function findBooksbyTitle(string $query)//:? Book
      {
          $builder = $this->createQueryBuilder('b');
          $builder
              ->where(
                  $builder->expr()->andX(
                      $builder->expr()->orX(
                          $builder->expr()->like('b.title', ':query'),
                          $builder->expr()->like('b.description', ':query'),
                      ),
                     //  $builder->expr()->isNotNull('m.released_at')
                  )
              )
              ->setParameter('query', '%' . $query . '%')
          ;
          return $builder
              ->getQuery()
              ->getResult();
      }
}
