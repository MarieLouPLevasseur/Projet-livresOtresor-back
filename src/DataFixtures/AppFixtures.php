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
            $categoriesArray = ['contes', 'science-fiction', 'fantasy','documentaire', 'policier', 'humour','philosophique','non-classé','BD'];

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
            // $userObj->setPassword('devinci');
            $userObj->setRole($roleObj);

            $manager->persist($userObj);

        // AVATARS
         // iswin= amount of book read to get the image
            $avatarsArray = [
                ['URL'=>'https://bombyxplm.com/wp-content/uploads/2021/01/421-4213053_default-avatar-icon-hd-png-download.png',
                'IsWin'=> 0],
                ['URL'=>'https://cdn.pixabay.com/photo/2016/11/18/23/38/child-1837375__340.png',
                'IsWin'=> 1],
                ['URL'=>'https://static.vecteezy.com/ti/vecteur-libre/t2/2002403-homme-avec-barbe-avatar-personnage-icone-isole-gratuit-vectoriel.jpg',
                'IsWin'=> 4],
                ['URL'=>'https://publicdomainvectors.org/tn_img/comic-boy.webp',
                'IsWin'=> 4],
                ['URL'=>'https://previews.123rf.com/images/gmast3r/gmast3r1411/gmast3r141100280/33645487-ic%C3%B4ne-de-profil-avatar-portrait-masculin-personne-d%C3%A9contract%C3%A9e.jpg',
                'IsWin'=> 7],
                ['URL'=>'https://icon-library.com/images/avatar-icon-images/avatar-icon-images-4.jpg',
                'IsWin'=> 7],
                ['URL'=>'https://cdn.icon-icons.com/icons2/2643/PNG/512/male_boy_person_people_avatar_icon_159358.png',
                'IsWin'=> 10],
                ['URL'=>'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR2QFaQyezndgniERWn-5S9oNrdXzK9yALQCj_V384ErrrH7il5bou3nGTREZCPMsoCjGY&usqp=CAU',
                'IsWin'=> 13],
                ['URL'=>'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTl6JPaMyl7e0oZfSdBa84_MTOUWwR50niJlLF79QPOlAIEYlSwWcWLG35W3EFI0iGzWFc&usqp=CAU',
                'IsWin'=> 16],
                ['URL'=>'https://cdn.iconscout.com/icon/free/png-256/avatar-372-456324.png',
                'IsWin'=> 19],
                ['URL'=>'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRrv3cNfOT8M33t5z7zr9Bu1uPpcAzgPFfRbMXeu9qmXMMExkaJ5vjULbA1MrLhNXy0ht8&usqp=CAU',
                'IsWin'=> 22],
                ['URL'=>'https://icon-library.com/images/icon-avatars/icon-avatars-12.jpg',
                'IsWin'=> 25],
                ['URL'=>'https://c8.alamy.com/compfr/p942gx/visage-de-l-homme-a-barbe-l-icone-d-avatar-illustration-man-show-pouce-vers-le-haut-p942gx.jpg',
                'IsWin'=> 28],

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
                ['URL'=>'https://img.freepik.com/vecteurs-premium/modele-certificat-diplome-mathematiques_1639-6717.jpg',
                'IsWin'=> 0],
                ['URL'=>'https://img.freepik.com/vecteurs-premium/diplome-enfant-certificat-contexte-remise-diplomes-prescolaire-mignon-jardin-enfants-modele-diplome-ecole-disposition-illustration-ludique-dessin-anime-vainqueur-vide-ballons-drapeaux_171867-152.jpg',
                'IsWin'=> 1],
                ['URL'=>'https://thumbs.dreamstime.com/z/mod%C3%A8le-de-certificat-dipl%C3%B4me-enfant-avec-ballons-couleur-color%C3%A9-des-et-gros-lettrage-pour-enfants-pr%C3%AAt-%C3%A0-imprimer-en-format-220927504.jpg',
                'IsWin'=> 4],
                ['URL'=>'https://thumbs.dreamstime.com/b/mod%C3%A8le-de-certificat-dipl%C3%B4me-enfant-avec-bulles-couleurs-color%C3%A9-ballons-couleur-soleil-ciel-et-grand-lettrage-pour-enfants-222295262.jpg',
                'IsWin'=> 4],
                ['URL'=>'https://img.freepik.com/vecteurs-premium/heureux-mignon-petit-garcon-enfant-tenant-trophee_97632-1775.jpg',
                'IsWin'=> 7],
                ['URL'=>'https://img.freepik.com/premium-vector/boy-kids-jumping-holding-trophy-book_1366-549.jpg?w=2000',
                'IsWin'=> 7],
                ['URL'=>'https://static.vecteezy.com/ti/vecteur-libre/p1/2181647-kid-making-success-poing-accomplissement-trophee-idee-dessin-anime-illustration-vectoriel.jpg',
                'IsWin'=> 10],
                ['URL'=>'https://media.istockphoto.com/vectors/kid-lifting-trophy-happy-cute-child-vector-id1197326945',
                'IsWin'=> 13],
                ['URL'=>'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSL2le2eq0YetgKiagPXxi4ZhFVnqKGOYO50JTYWTv1tHVnOzPN6VonEUb2If1EVd_1I6o&usqp=CAU',
                'IsWin'=> 16],
                ['URL'=>'https://image.shutterstock.com/z/stock-vector--you-win-congratulations-banner-with-balloons-win-game-birthday-party-sale-holiday-kid-448182349.jpg',
                'IsWin'=> 19],
                ['URL'=>'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS069_IAXaenRfeBvwU6PAdWLakBq7FlBUebA&usqp=CAU',
                'IsWin'=> 22],
                ['URL'=>'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQvR1R6lEkcjQYusPlenlq3w4iEU9zqe0nRRw&usqp=CAU',
                'IsWin'=> 25],
                ['URL'=>'https://us.123rf.com/450wm/tigatelu/tigatelu1310/tigatelu131000161/23006583-cartoon-little-boy-c%C3%A9l%C3%A8bre-sa-m%C3%A9daille-d-or.jpg',
                'IsWin'=> 28],

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
