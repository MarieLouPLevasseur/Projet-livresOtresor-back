<?php

namespace App\DataFixtures;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use App\Entity\Kid;
use App\Entity\Book;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Author;
use App\Entity\Avatar;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture implements DependentFixtureInterface
{

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function getDependencies()
    {
        return [
            AppFixtures::class,
        ];
    }
   

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        $userRole= $manager->getRepository(Role::class)->findOneByRoleName('ROLE_USER');

        // USERS

          $nbUsers= 15;
          for ($i = 0; $i < $nbUsers; $i++) {
              $userObj = new User();

              $userObj->setFirstname($faker->firstname());
              $userObj->setLastname($faker->lastname());
              $userObj->setEmail($faker->unique->email());



            //   $hashedPassword = $this->passwordHasher->hashPassword($userObj, 'Devinci!'.$i);
              $hashedPassword = $this->passwordHasher->hashPassword($userObj, 'devinci');
              $userObj->setPassword($hashedPassword);
            //   $userObj->setPassword('devinci');
              $userObj->setRole($userRole);
          

              $manager->persist($userObj);
          }
          $manager->flush();


        // KIDS
            $nbKids= 25;
            $kidRole= $manager->getRepository(Role::class)->findOneByRoleName('ROLE_KID');
            $kidProfil= $manager->getRepository(Avatar::class)->findOneByIsWinValue(0);
            $allUsers= $manager->getRepository(User::class)->findAll();

                for ($i = 0; $i < $nbKids; $i++) {
                    $kidObj = new Kid();

                    $kidObj->setUsername($faker->firstname().random_int(3,999));
                    $kidObj->setFirstname($faker->firstname());

                    $hashedPassword = $this->passwordHasher->hashPassword($kidObj, 'devinci');
                    $kidObj->setPassword($hashedPassword);
                    $kidObj->setRole($kidRole);
                    $kidObj->setProfileAvatar($kidProfil->getUrl());

                // Random User selection

                        $randomUser = $faker->randomElement($allUsers);

                        $kidObj->SetUser($randomUser);

                $manager->persist($kidObj);
               }

            
 
        $manager->flush();
            

 
    }

}

  