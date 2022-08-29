<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function add(Author $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Author $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
    * Find an Author by name
    * 
    * @param array $author_name 
    * @return Author[] Returns an array of Author objects
    */
   public function findAuthorByName($author_name): array
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.name = :val')
           ->setParameter('val', $author_name)
           ->getQuery()
           ->getResult()
       ;
   }
    

    /** 
    *Find Book_Kid by Kid and Book
    *
    * @param int $book_id
    * @param int $author_id
    * @return mixed
    */
    public function findOneByKidandBook($kid_id, $book_id)
    {
             $entityManager = $this->getEntityManager();
             $query = $entityManager->createQuery(

                'SELECT bk, 
                 FROM App\Entity\BookKid bk
                 JOIN bk.kid k
                 JOIN bk.book b
                 JOIN bk.author a
                 WHERE k.id = :kid_id AND b.id = :book_id'
             )

             ->setParameters(array('kid_id'=> $kid_id, 'book_id' => $book_id));
 
             return $query->getResult();
 
    }
}
