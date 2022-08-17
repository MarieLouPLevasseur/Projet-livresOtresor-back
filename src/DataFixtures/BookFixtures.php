<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\BookKid;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


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
                $bookObj->setTitle($faker->text());
                $bookObj->setDescription($faker->text());
                $bookObj->setPublisher($faker->sentence(5));

                $authorObj = new Author();
                $authorObj->setName($faker->name());
                $manager->persist($authorObj);

                $bookObj->addAuthor($authorObj);

                $manager->persist($bookObj);
            }

       
            

        $manager->flush();
            
    }

}