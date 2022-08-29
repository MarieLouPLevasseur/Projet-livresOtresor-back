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
            $nbBooks = 130;

            $allCategories= $manager->getRepository(Category::class)->findAll();
            $allKids= $manager->getRepository(Kid::class)->findAll();
          

            for ($i = 0; $i < $nbBooks; $i++) {
                $bookObj = new Book();

                $bookObj->setIsbn(mt_rand(1000000000000,9999999999999));
                $bookObj->setTitle("title #:".$i." ".$faker->text());
                $bookObj->setDescription($faker->text());
                $bookObj->setPublisher("Editeur #:".$i." ".$faker->sentence(5));
                $bookObj->setCover("https://i.pinimg.com/564x/11/1b/59/111b5913903c2bfbe7f11487bb3f06f6.jpg");

                    // set author
                    $authorObj = new Author();
                    $authorObj->setName($faker->name());
                    $bookObj->addAuthor($authorObj);
                
                
                    // set Book_kids
                    $bookKidObj = new BookKid();
                    $bookKidObj->setIsRead(mt_rand(0,1));
                    $bookKidObj->setKid($faker->randomElement($allKids));
                    $bookObj->addBookKid($bookKidObj);


                $manager->persist($authorObj);     
                $manager->persist($bookKidObj);
                $manager->persist($bookObj);
            }
        
            $manager->flush();
              

            // options of the kid
              $allBookKid= $manager->getRepository(BookKid::class)->findAll();

                for ($i=0; $i<20; $i++){
                    $randomBookKid = $faker->randomElement($allBookKid);
                    $randomBookKid->setComment($faker->text());
                    $randomBookKid->setRating($faker->randomDigit(0,5));
                    $randomBookKid->setCategory($faker->randomElement($allCategories));
                    
                    $manager->persist($randomBookKid);
            }

            
            //  FOR 3 kids : set many books for tests

            $allBooks= $manager->getRepository(Book::class)->findAll();    

                // 10 books read
                $KidOne = $faker->randomElement($allKids);

                for ($i = 0 ; $i<10; $i++){

                    $bookKidObj = new BookKid();
                    $bookKidObj->setIsRead(1);
                    $bookKidObj->setKid($KidOne);
                    $bookKidObj->setBook($faker->randomElement($allBooks));
                    $bookKidObj->setComment($faker->text());
                    $bookKidObj->setRating($faker->randomDigit(0,5));
                    $bookKidObj->setCategory($faker->randomElement($allCategories));

                    $manager->persist($bookKidObj);

                }

            // 20 books read
                $KidTwo = $faker->randomElement($allKids);

                for ($i = 0 ; $i<20; $i++){

                    $bookKidObj = new BookKid();
                    $bookKidObj->setIsRead(1);
                    $bookKidObj->setKid($KidTwo);
                    $bookKidObj->setBook($faker->randomElement($allBooks));
                    $bookKidObj->setComment($faker->text());
                    $bookKidObj->setRating($faker->randomDigit(0,5));
                    $bookKidObj->setCategory($faker->randomElement($allCategories));

                    $manager->persist($bookKidObj);

            }

            // 60 books read
                $KidThree = $faker->randomElement($allKids);

                for ($i = 0 ; $i<60; $i++){

                    $bookKidObj = new BookKid();
                    $bookKidObj->setIsRead(1);
                    $bookKidObj->setKid($KidThree);
                    $bookKidObj->setBook($faker->randomElement($allBooks));
                    $bookKidObj->setComment($faker->text());
                    $bookKidObj->setRating($faker->randomDigit(0,5));
                    $bookKidObj->setCategory($faker->randomElement($allCategories));

                    $manager->persist($bookKidObj);

                }


        $manager->flush();
            
    }

}