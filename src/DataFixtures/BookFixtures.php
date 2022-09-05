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

        // Datas URL book Cover
            $bookCovers = [
                "https://i.pinimg.com/236x/b2/e2/b7/b2e2b7eb7cf1583483256220385a2710.jpg",
                "https://assets.hongkiat.com/uploads/children-book-covers/pete-the-cat.jpg",
                "https://i.pinimg.com/236x/c1/be/11/c1be11ba85e57689349fe49a04e148f2.jpg",
                "https://assets.hongkiat.com/uploads/children-book-covers/breadcrumbs.jpg",
                "https://i.pinimg.com/236x/a4/01/55/a4015547b4cb57dfea5b8ac6e3698d9e.jpg",
                "https://fr.shopping.rakuten.com/photo/Rowling-Joanne-Kathleen-Harry-Potter-T-1-Harry-Potter-A-L-ecole-Des-Sorciers-Livre-929310487_ML.jpg",
                "https://images-eu.ssl-images-amazon.com/images/W/WEBP_402378-T1/images/I/51jH5ABK7lL._SX218_BO1,204,203,200_QL40_ML2_.jpg",
                "https://cms-tc.pbskids.org/parents/_generic600Wide/Cooking-in-a-Can.jpg",
                "http://www.idboox.com/wp-content/uploads/2014/07/harry-potter-prisonnier-Azkaban-nouvelle-couverture-ebook-IDBOOX.jpg",
                "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSFe3R7bcbPlBUi_mej0GBjknsmwglTf33AdA&usqp=CAU",
                "http://static.hitek.fr/img/actualite/hp-hallows-duddle.jpg",
                "http://static.hitek.fr/img/actualite/hp-half-blood-duddle.jpg",
                "http://static.hitek.fr/img/actualite/hp-chamber-duddle.jpg",
                "http://static.hitek.fr/img/actualite/hp-phoenix-duddle.jpg",
                "https://i1.wp.com/tintinomania.com/wp-content/uploads/2019/08/Couv-ile-noire-1938.jpg?resize=500%2C648&ssl=1",
                "https://i.pinimg.com/originals/7a/48/83/7a4883d5a5834effdaad5609ceb964fe.jpg",
                "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQMgP2rZhOAyKJmm7ID5F2CS8L5-B1RyAjQunHpQwj-qT1WAFob_7irU8p2HKwJallnq6U&usqp=CAU",
                "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTdBq2bhxnox23Ilfdz1NwULFEpN5mzTGeFCZ_l-3uSx1PrWz4g-bGaB3uvwIAPDmvxNro&usqp=CAU",
                "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcST_jrNNAMT17slUbJlfrQyDEs_fwuEiOjxMFdYe5NmsiKsbhbkEYoqTxBLptZkNkVORjo&usqp=CAU",
                "https://i.ebayimg.com/images/a/(KGrHqN,!hEF!lU1Hun6BQKmovyWF!~~/s-l500.jpg",
                "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTJf3RpksvmdFAkEh3GBEcdkLUEoKhpd6oC_-uvW7Dls7yVPeuK6AVsdaxkwYs_5Vd8CU&usqp=CAU",
                "https://cdn001.tintin.com/public/tintin/img/static/tintin-au-tibet/tintin-au-tibet-cover-fr.jpg",
                "https://www.minus-editions.fr/1240-thickbox_default/papi-mamie-et-moi.jpg",
                "https://images.lpcdn.ca/924x615/201710/13/1483019-tour-gaule.jpg",
                "https://www.creatopy.com/blog/wp-content/uploads/2020/07/Hey-Grandude.jpg",
                "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRxKF4vQFS1A_62WAEV3reXvN5xhtbtLuijxW8cEF1U67AHy3GInu1PHYyeJnOo-43Dq28&usqp=CAU",
                "https://hakaimagazine.com/wp-content/uploads/ugly-place-roundsup-spring-2022.jpg",
                "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRESDa2X4CD0ROdiuNIh2IZlGh9BJdcKQlHOryOdj1Cvf2InO96jlHd1pgPr1h8GS_tEXI&usqp=CAU",
                "https://static1.lecteurs.com/media/cache/book_large/files/books-covers/388/9782012101388_1_75.jpg",
                "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqmy6a7Z0f--pOim1O9X_Y0A5r-9AFLuZPsq2kQhtVRw7oogIAubW1YRZtNt_psq0d5og&usqp=CAU",
                "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR21GQ2UnUTB7A6ZkWyTTFkuvuImo_fs97yfQ0blEy0jxRqVlmkEyMvRku7H_yIN2leF6c&usqp=CAU",
                "https://d2n9ha3hrkss16.cloudfront.net/uploads/stage/stage_image/37836/optimized_large_thumb_stage.jpg",
                "https://static1.lecteurs.com/media/cache/book_large/files/books-covers/555/9782012101555_1_75.jpg",
                "https://static.wikia.nocookie.net/gardiens-des-cites-perdue/images/f/fd/Nocturna.jpg/revision/latest?cb=20180418180415&path-prefix=fr",
                "https://i.pinimg.com/originals/f7/6a/61/f76a61440698a93d40f1947be5b8ab52.png",
                "https://static.wikia.nocookie.net/gardiens-des-cites-perdue/images/5/56/Gardiens-des-cites-perdues-tome-8-heritages-1251237.jpg/revision/latest?cb=20200503144144&path-prefix=fr",
                "https://cdn1.booknode.com/book_cover/543/full/gardiens-des-cites-perdues-tome-2-exil-543167.jpg",
                "https://images-na.ssl-images-amazon.com/images/I/71HeAUQ8fCL.jpg",
                "https://img.livraddict.com/covers/328/328854//couv25249105.jpg",
                "https://1.bp.blogspot.com/-IjkcUu43jfI/WaVXZCMCrSI/AAAAAAAAaXg/Uvy_LbqMNHkV0vpaEgDML2_R5I6CC0NuwCLcBGAs/s1600/81Rw0ppphZL.jpg",
                "https://www.lecteurs.com/media/cache/resolve/book_large/files/books-covers/276/9782371020276_1_75.jpg",
                "https://img.livraddict.com/covers/305/305682//couv29542547.jpg",
                "https://img.livraddict.com/covers/300/300770//couv65284137.jpg",
                "https://www.editions-harmattan.fr/catalogue/couv/b/2747540588b.jpg",
                "https://i.pinimg.com/originals/54/8d/32/548d32706863cf9ad8f96043c5a8b651.jpg",
                "https://blog-cdn.reedsy.com/uploads/2019/12/where-are-you-from-1024x874.jpg",
                "https://img.livraddict.com/covers/136/136701/couv37252445.jpg",
                "https://images-na.ssl-images-amazon.com/images/I/51FawVap8+L._SX344_BO1,204,203,200_.jpg",
                "https://cdn1.booknode.com/book_cover/1061/mod11/la_cabane_magique_tome_26_a_la_recherche_de_lepee_de_lumiere-1060801-264-432.jpg",
                "https://imagine.bayard.io/unsafe/560x0/bayard-static/edition/couvertures/9791036336058/9791036336058.jpg",
                "https://www.lecteurs.com/media/cache/resolve/book_large/files/books-covers/364/9782747018364_1_75.jpg",
                "https://imagine.bayard.io/unsafe/268x0/bayard-static/edition/couvertures/9791036332890/9791036332890.jpg",
                "https://s18670.pcdn.co/wp-content/uploads/littlered.jpg",
                "https://cdn1.booknode.com/book_cover/1061/la_cabane_magique_tome_38_au_pays_des_farfadets-1060825-264-432.jpg",
                "https://www.lecteurs.com/media/cache/resolve/book_large/files/books-covers/581/9791036324581_1_75.jpg",
                "https://cdn1.booknode.com/book_cover/1044/mod11/la_cabane_magique_tome_42_rendez_vous_avec_le_president_lincoln-1044270-264-432.jpg",
                "https://getcovers.com/wp-content/uploads/2020/12/image36.jpg",
                "https://imagine.bayard.io/unsafe/268x0/bayard-static/edition/couvertures/9791036324475/9791036324475.jpg",
                "https://images-eu.ssl-images-amazon.com/images/W/WEBP_402378-T1/images/I/51wK7nAy3cL._SY291_BO1,204,203,200_QL40_ML2_.jpg",
                "https://mariebambelle.fr/2652-large_default/le-dragon-myst%C3%A9rieux-livre-personnalis%C3%A9-pour-enfant.jpg",
                "https://images-eu.ssl-images-amazon.com/images/W/WEBP_402378-T1/images/I/51yIgbk1QEL._SY291_BO1,204,203,200_QL40_ML2_.jpg",
                "https://coucoufrenchclasses.com/wp-content/uploads/2020/08/Screen-Shot-2020-08-25-at-3.44.42-PM-498x700.png",
                "https://images-eu.ssl-images-amazon.com/images/W/WEBP_402378-T1/images/I/51VQ2dX5ZfL._SY291_BO1,204,203,200_QL40_ML2_.jpg",
                "https://cdn1.booknode.com/book_cover/1298/mod11/maitre_des_dragons_tome_8_le_cri_du_dragon_du_tonnerre-1298153-264-432.jpg",
                "https://static.fnac-static.com/multimedia/Images/FR/NR/a8/8f/8b/9146280/1507-1/tsp20220625073744/Maitres-des-dragons.jpg",
                "https://images-na.ssl-images-amazon.com/images/I/81QGh4hT1DL.jpg",
                "https://cdn1.booknode.com/book_cover/1501/mod11/maitres_des_dragons_tome_11_la_mission_du_dragon_dargent-1500701-264-432.jpg",
                "https://imagine.bayard.io/unsafe/268x0/bayard-static/edition/couvertures/9782747067577/9782747067577.jpg",
                "https://coucoufrenchclasses.com/wp-content/uploads/2020/08/Screen-Shot-2020-08-25-at-3.44.52-PM-564x700.png",
                "https://imagine.bayard.io/unsafe/268x0/bayard-static/edition/couvertures/9782747067584/9782747067584.jpg"
            
            ];
            

        // BOOKS
            $nbBooks = 130;

            $allCategories= $manager->getRepository(Category::class)->findAll();
            $allKids= $manager->getRepository(Kid::class)->findAll();
          

            for ($i = 0; $i < $nbBooks; $i++) {
                $bookObj = new Book();

                $bookObj->setIsbn(mt_rand(1000000000000,9999999999999));
                // $bookObj->setTitle("title #:".$i." ".$faker->text());
                $bookObj->setTitle($faker->sentence(mt_rand(3,10)));
                $bookObj->setDescription($faker->text());
                $bookObj->setPublisher($faker->sentence(mt_rand(1,3)));

                // Cover
                // $coverRandomKey = (array_rand($bookCovers));
                // $randomCoverUrl = $bookCovers[$coverRandomKey];
                // $bookObj->setCover("https://i.pinimg.com/564x/11/1b/59/111b5913903c2bfbe7f11487bb3f06f6.jpg");
                $bookObj->setCover($faker->randomElement($bookCovers));

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