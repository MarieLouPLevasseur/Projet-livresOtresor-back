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
            $roleArray = ['ROLE_KID','ROLE_USER','ROLE_ADMIN'];

                foreach($roleArray as $role){

                    $roleObj = new Role();
                    $roleObj->setName($role);

                    $manager->persist($roleObj);
                }
                $manager->flush();

        // CATEGORIES
            $categoriesArray = ['Non-classé', 'Aventure','BD','Contes','Documentaires', 'Fantastique','Humour', 'Policier', 'Philosophique','Science-fiction'];

                foreach($categoriesArray as $category){

                    $categoryObj = new Category();

                    $categoryObj->setName($category);
                    $manager->persist($categoryObj);
                }
        // Set ADMIN
        $userRole= $manager->getRepository(Role::class)->findOneByRoleName('ROLE_ADMIN');


        // USER FOR API
            $userObj = new User();

            $userObj->setFirstname('equipe');
            $userObj->setLastname('front');
            $userObj->setEmail('livresOtresor@apotheose.com');
            
            $hashedPassword = $this->passwordHasher->hashPassword($userObj, 'Devinci!00');
            $userObj->setPassword($hashedPassword);
            $userObj->setRole($userRole);

            $manager->persist($userObj);

            $avatarsArray = [
                ['URL' => '/img/avatars/0default_avatar.png', 'IsWin' => 0],
                ['URL' => '/img/avatars/1monstre_violet.png', 'IsWin' => 1],
                ['URL' => '/img/avatars/2monstre_bleu.png', 'IsWin' => 4],
                ['URL' => '/img/avatars/3monstre_bleu.png', 'IsWin' => 7],
                ['URL' => '/img/avatars/4monstre_rose.png', 'IsWin' => 10],
                ['URL' => '/img/avatars/5monstre_violet.png', 'IsWin' => 13],
                ['URL' => '/img/avatars/6monstre_rose.png', 'IsWin' => 16],
                ['URL' => '/img/avatars/7poisson_orange.png', 'IsWin' => 19],
                ['URL' => '/img/avatars/8pieuvre_orange.png', 'IsWin' => 23],
                ['URL' => '/img/avatars/9poisson_violet.png', 'IsWin' => 28],
                ['URL' => '/img/avatars/10fille.png', 'IsWin' => 33],
                ['URL' => '/img/avatars/11garcon.png', 'IsWin' => 33],
                ['URL' => '/img/avatars/12garcon.png', 'IsWin' => 38],
                ['URL' => '/img/avatars/13fille.png', 'IsWin' => 38],
                ['URL' => '/img/avatars/14garcon.png', 'IsWin' => 43],
                ['URL' => '/img/avatars/15fille.png', 'IsWin' => 43],
                ['URL' => '/img/avatars/16garcon.png', 'IsWin' => 48],
                ['URL' => '/img/avatars/17fille.png', 'IsWin' => 48],
                ['URL' => '/img/avatars/18garcon.png', 'IsWin' => 53],
                ['URL' => '/img/avatars/19monstre_lunette.png', 'IsWin' => 58],
                ['URL' => '/img/avatars/20monstre_lunette.png', 'IsWin' => 63],
                ['URL' => '/img/avatars/21cat_black.png', 'IsWin' => 68],
                ['URL' => '/img/avatars/22-a_cat_white.png', 'IsWin' => 73],
                ['URL' => '/img/avatars/22-b_cat_white.png', 'IsWin' => 78],
                ['URL' => '/img/avatars/23lion.png', 'IsWin' => 83],
                ['URL' => '/img/avatars/24dog.png', 'IsWin' => 88],
                ['URL' => '/img/avatars/25chouette.png', 'IsWin' => 93],
                ['URL' => '/img/avatars/26monster_music.png', 'IsWin' => 98],
                ['URL' => '/img/avatars/27dragon.png', 'IsWin' => 98],
                ['URL' => '/img/avatars/28monster.png', 'IsWin' => 103],
                ['URL' => '/img/avatars/29monster_red.png', 'IsWin' => 108],
                ['URL' => '/img/avatars/30monster_green.png', 'IsWin' => 113]
            ];
                foreach($avatarsArray as $avatar){
                    $avatarObj= new Avatar();

                    $avatarObj->setUrl($avatar["URL"]);
                    $avatarObj->setIsWin($avatar["IsWin"]);

                    $manager->persist($avatarObj);
                }

                $diplomasArray = [
                    ['url'=>'/img/diplomes/diplome_1.png',
                    'isWin'=> 1],
                    ['url'=>'/img/diplomes/diplome_10.png',
                    'isWin'=> 10],
                    ['url'=>'/img/diplomes/diplome_20.png',
                    'isWin'=> 20],
                    ['url'=>'/img/diplomes/diplome_30.png',
                    'isWin'=> 30],
                    ['url'=>'/img/diplomes/diplome_40.png',
                    'isWin'=> 40]
        
                ];
                foreach($diplomasArray as $diploma){
                    $diplomaObj= new Diploma();

                    $diplomaObj->setUrl($diploma["url"]);
                    $diplomaObj->setIsWin($diploma["isWin"]);

                    $manager->persist($diplomaObj);
                }
       $manager->flush();
    }

}
