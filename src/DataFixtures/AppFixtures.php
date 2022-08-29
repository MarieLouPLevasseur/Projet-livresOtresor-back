<?php

namespace App\DataFixtures;

use App\Entity\Avatar;
use App\Entity\Category;
use App\Entity\Diploma;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class AppFixtures extends Fixture
{


    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        
        // ROLES
            $roleArray = ['ROLE_KID','ROLE_USER'];

                foreach($roleArray as $role){

                    $roleObj = new Role();
                    $roleObj->setName($role);

                    $manager->persist($roleObj);
                }

        // CATEGORIES
            $categoriesArray = ['Non-classé', 'Aventure','BD','Contes','Documentaires', 'Fantastique','Humour', 'Policier', 'Philosophique','Science-fiction'];

                foreach($categoriesArray as $category){

                    $categoryObj = new Category();

                    $categoryObj->setName($category);
                    $manager->persist($categoryObj);
                }

        // USER FOR API
            $userObj = new User();

            $userObj->setFirstname('equipe');
            $userObj->setLastname('front');
            $userObj->setEmail('livresOtresor@apotheose.com');
            
            $hashedPassword = $this->passwordHasher->hashPassword($userObj, 'devinci');
            $userObj->setPassword($hashedPassword);
            $userObj->setRole($roleObj);

            $manager->persist($userObj);


        // AVATARS
         // iswin= amount of book read to get the image
            $avatarsArray = [
                ['URL'=>'https://zupimages.net/up/22/34/7g4i.png',
                'IsWin'=> 0],
                ['URL'=>'https://zupimages.net/up/22/34/e9mx.png',
                'IsWin'=> 1],
                ['URL'=>'https://zupimages.net/up/22/34/iyfi.png',
                'IsWin'=> 4],
                ['URL'=>'https://zupimages.net/up/22/34/mcth.png',
                'IsWin'=> 7],
                ['URL'=>'https://zupimages.net/up/22/34/k9ko.png',
                'IsWin'=> 10],
                ['URL'=>'https://zupimages.net/up/22/34/uadk.png',
                'IsWin'=> 13],
                ['URL'=>'https://zupimages.net/up/22/34/efu4.png',
                'IsWin'=> 16],
                ['URL'=>'https://zupimages.net/up/22/34/m1yr.png',
                'IsWin'=> 19],
                ['URL'=>'https://zupimages.net/up/22/34/ghr9.png',
                'IsWin'=> 23],
                ['URL'=>'https://zupimages.net/up/22/34/v8hz.png',
                'IsWin'=> 28],
                ['URL'=>'https://zupimages.net/up/22/34/h1rm.png',
                'IsWin'=> 33],
                ['URL'=>'https://zupimages.net/up/22/34/i9gr.png',
                'IsWin'=> 33],
                ['URL'=>'https://zupimages.net/up/22/34/4qva.png',
                'IsWin'=> 38],
                ['URL'=>'https://zupimages.net/up/22/34/4qva.png',
                'IsWin'=> 38],
                ['URL'=>'https://zupimages.net/up/22/34/2nf1.png',
                'IsWin'=> 43],
                ['URL'=>'https://zupimages.net/up/22/34/k70i.png',
                'IsWin'=> 43],
                ['URL'=>'https://zupimages.net/up/22/34/y139.png',
                'IsWin'=> 48],
                ['URL'=>'https://zupimages.net/up/22/34/1x4x.png',
                'IsWin'=> 48],
                ['URL'=>'https://zupimages.net/up/22/34/xst4.png',
                'IsWin'=> 53],
                ['URL'=>'https://zupimages.net/up/22/34/4x7f.png',
                'IsWin'=> 53],
                ['URL'=>'https://zupimages.net/up/22/34/ceve.png',
                'IsWin'=> 58],
                ['URL'=>'https://zupimages.net/up/22/34/q3t5.png',
                'IsWin'=> 63],
                ['URL'=>'hhttps://zupimages.net/up/22/34/6yp7.png',
                'IsWin'=> 68],
                ['URL'=>'https://zupimages.net/up/22/34/b1dr.png',
                'IsWin'=> 73],
                ['URL'=>'https://zupimages.net/up/22/34/jsyv.png',
                'IsWin'=> 78],
                ['URL'=>'https://zupimages.net/up/22/34/mexa.png',
                'IsWin'=> 83],
                ['URL'=>'https://zupimages.net/up/22/34/8d6j.png',
                'IsWin'=> 88],
                ['URL'=>'https://zupimages.net/up/22/34/1e19.png',
                'IsWin'=> 93],
                ['URL'=>'https://zupimages.net/up/22/34/y380.png',
                'IsWin'=> 98],
                ['URL'=>'https://zupimages.net/up/22/34/0zvc.png',
                'IsWin'=> 98],
                ['URL'=>'https://zupimages.net/up/22/34/bfc8.png',
                'IsWin'=> 103],
                ['URL'=>'https://zupimages.net/up/22/34/reoy.png',
                'IsWin'=> 108],
                ['URL'=>'https://zupimages.net/up/22/34/w2j0.png',
                'IsWin'=> 113],

            ];

                foreach($avatarsArray as $avatar){
                    $avatarObj= new Avatar();

                    $avatarObj->setUrl($avatar["URL"]);
                    $avatarObj->setIsWin($avatar["IsWin"]);

                    $manager->persist($avatarObj);
                }

        // DIPLOMAS
          // iswin= amount of book read to get the image
            $diplomasArray = [
                ['URL'=>'https://zupimages.net/up/22/34/03gf.png',
                'IsWin'=> 1],
                ['URL'=>'https://zupimages.net/up/22/34/se8q.png',
                'IsWin'=> 10],
                ['URL'=>'https://zupimages.net/up/22/34/ug9y.png',
                'IsWin'=> 20],
                ['URL'=>'https://zupimages.net/up/22/34/anu4.png',
                'IsWin'=> 30],
                ['URL'=>'https://zupimages.net/up/22/34/v70m.png',
                'IsWin'=> 40]

            ];

                foreach($diplomasArray as $diploma){
                    $diplomaObj= new Diploma();

                    $diplomaObj->setUrl($diploma["URL"]);
                    $diplomaObj->setIsWin($diploma["IsWin"]);

                    $manager->persist($diplomaObj);
                }



       $manager->flush();
    }

}
