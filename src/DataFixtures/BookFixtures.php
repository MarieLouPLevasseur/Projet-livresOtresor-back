<?php

namespace App\DataFixtures;

use App\Entity\Kid;
use App\Entity\Book;
use App\Entity\Author;
use App\Entity\BookKid;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class BookFixtures extends Fixture implements DependentFixtureInterface
{

    public function getDependencies()
    {
        return [
            AppFixtures::class,
            UserFixtures::class,
        ];
    }
   

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        // BOOKS
            $nbBooks = 30;


            for ($i = 0; $i < $nbBooks; $i++) {
                $bookObj = new Book();

                $bookObj->setIsbn(mt_rand(1000000000000,9999999999999));
                $bookObj->setTitle("title #:".$i." ".$faker->text());
                $bookObj->setDescription($faker->text());
                $bookObj->setPublisher("Editeur #:".$i." ".$faker->sentence(5));

                // set author
                $authorObj = new Author();
                $authorObj->setName($faker->name());
                $bookObj->addAuthor($authorObj);
                $manager->persist($authorObj);           

                $manager->persist($bookObj);
            }
            $manager->flush();


        //BOOK_KIDS
            $allCategories= $manager->getRepository(Category::class)->findAll();
            $allKids= $manager->getRepository(Kid::class)->findAll();
            $allBooks= $manager->getRepository(Book::class)->findAll();


            $nbBooksKids = 35;

                for ($i = 0; $i < $nbBooks; $i++) {
                    $bookKidObj = new BookKid();
                    $bookKidObj->setIsRead(mt_rand(0,1));
                    $bookKidObj->setKid($faker->randomElement($allKids));
                    $bookKidObj->setBook($faker->randomElement($allBooks));

                    $manager->persist($bookKidObj);
                }
            
            $manager->flush();


            // options of the kid
              $allBookKid= $manager->getRepository(BookKid::class)->findAll();
                // dd($allBookKid);
                for ($i=0; $i<20; $i++){
                    $randomBookKid = $faker->randomElement($allBookKid);
                    $randomBookKid->setComment($faker->text());
                    $randomBookKid->setRating($faker->randomDigit(0,5));
                    $randomBookKid->setCategory($faker->randomElement($allCategories));
                    
                    $manager->persist($randomBookKid);
            }
            


        $manager->flush();
            
    }

}