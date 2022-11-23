<?php

namespace App\Controller;

use App\Entity\Kid;
use App\Entity\User;
use App\Repository\KidRepository;
use Symfony\Component\Mime\Email;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Repository\AvatarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// use Symfony\Component\Validator\Constraints\Email;

/**
 *  @Route("/api/v1", name="api_user")
 */
class UserController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Add a user (registration)
     *
     * @Route("/registration", name="create", methods="POST")
     */
    public function createUser( 
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        RoleRepository $roleRepository,
        MailerInterface $mailer
        ): Response
    {

        $data = $request->getcontent();
        
        $user = $serializer->deserialize($data, User::class, 'json');

        // CHECK datas given

        $errors = $validator->validate($user);


        if (count($errors) > 0) {
           
            return $this->ErrorMessageNotValid($errors);

        }

            $role = $roleRepository->findOneByRoleName("ROLE_USER");
            $user->setRole($role);
    
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user ->setPassword($password);
    
          
            $em->persist($user);
            $em->flush();

            // $email = (new Email())        
            //     ->from('livresOtresor@apotheose.com')
            //     ->to($user->getEmail())
            //     ->subject("Bienvenue sur le site de livre'O'Trésor")
            //     ->text("Nous sommes heureux de vous compter parmis nos membres");
            // $mailer->send($email);
            
            $email = (new TemplatedEmail())
                ->from(new Address('livresOtresor@apotheose.com', 'Livres O Trésor'))
                ->to(new Address ($user->getEmail(), $user->getFirstName()))
                ->subject('Bienvenue sur le site de livres O Trésor!')
                // ->text("Nous sommes content de vous compter parmis nos membres {$user->getFirstName()}! ❤️")
                // ->html("<h1>Nous sommes content de vous compter parmis nos membres {$user->getFirstName()}! ❤️</h1>");
                ->htmlTemplate('email/welcome.html.twig')
                ->context([
                    'user'=>$user
                ]);
                
                $mailer->send($email);

            $message = [
                'error' => false,
                'message' => "L'utilisateur a bien été enregistré"
            ];
            return $this->json($message, 201);
    }


    /**
     * Show datas of a user
     * 
     * @Route("/users/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function show(int $id, UserRepository $userRepository) :Response
    {
 
        $user = $userRepository->find($id);

        // CHECK USER exists

        if ($user === null )
        {

            return $this->ErrorMessageNotFound("The user not found for id: ", $id);

        }

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user_list']);
    }


    /**
     * List all kids by user
     *
     * @Route("/users/{id}/kids", name="listkids", methods="GET", requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function listKids( int $id, UserRepository $userRepository, KidRepository $kidRepository): Response
    {
        $user = $userRepository->find($id);

        // CHECK USER exists

        if ($user === null )
        {
            return $this->ErrorMessageNotFound("The user not found for id: ", $id);

        }

        $listKid = $user->getKid();
        
        return $this->json($listKid, 200, [], ['groups' => 'userkids_list']);
    }


    /** 
     * Create a Kid
     * 
     * @Route("/users/{id}/kids", name="create_kid", methods="POST", requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function createKid( int $id, 

        EntityManagerInterface $em, 
        Request $request, 
        UserRepository $userRepository, 
        SerializerInterface $serializer,
        RoleRepository $roleRepository,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        AvatarRepository $avatarRepository
        ):Response

    
    {
        $user = $userRepository->find($id);
        $kidData = $request->getcontent();
        $kidData = $serializer->deserialize($kidData, Kid::class, 'json');
        $role = $roleRepository->findOneByRoleName("ROLE_KID");        
        $kidData->setRole($role);
        $kidData->setUser($user);

        $avatar = $avatarRepository->findOneByIsWinValue(0);
        $kidData->setProfileAvatar($avatar->getUrl()); 
       
        // CHECK USER exists

        if ($user === null )
        {
            return $this->ErrorMessageNotFound("The user not found for id: ", $id);

        }

        // CHECK datas given

        $errors = $validator->validate($kidData);

        if (count($errors) > 0) {
          
            return $this->ErrorMessageNotValid($errors);

        }

        $password = $passwordHasher->hashPassword($kidData, $kidData->getPassword());
        $kidData->setPassword($password);

        $em->persist($kidData);
        $em->flush();

        $message = [
            'error' => false,
            'message' => "L'enfant a bien été enregistré"
        ];


        return $this->json($message, Response::HTTP_OK);        

    }

    /** 
     * Update a kid
     * 
     * @Route("/users/{id_user}/kids/{id_kid}", name="update_kid", methods="PATCH", requirements={"id_user"="\d+", "id_kid"="\d+"})
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function updateKid(
        int $id_user,
        int $id_kid,
        EntityManagerInterface $em, 
        UserRepository $userRepository,
        Request $request, 
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        KidRepository $kidRepository,
        UserPasswordHasherInterface $passwordHasher
        )
    {
        $user = $userRepository->find($id_user);
        $kid = $kidRepository->find($id_kid);

        // CHECK USER exists
        
            if ($user === null )
            {
                return $this->ErrorMessageNotFound("The user not found for id: ", $id_user);

            }

        // CHECK KID exists

            if ($kid === null )
            {
                return $this->ErrorMessageNotFound("The Kid not found for id: ", $id_kid);

            }

        $data = $request->getContent();
        $dataKid = $serializer->deserialize($data, Kid::class, 'json');

        // ******** CHECK FIRSTNAME *********


        if($dataKid->getFirstname() !== null){


            // CHECK datas given

            $errors = $validator->validatePropertyValue($dataKid, 'firstname', $dataKid->getFirstname());
            if ((count($errors) > 0) ){
               
                return $this->ErrorMessageNotValid($errors);

            }   

            // set
            $kid->setFirstname($dataKid->getFirstname());
        } 
        // ******** CHECK USERNAME *********


        if($dataKid->getUsername() !== null){

            // CHECK USERNAME already exists

            $nameGiven = $dataKid->getUsername();
            $nameAlreadyExist = $kidRepository->findBy(["username"=>$nameGiven]);

            if($nameAlreadyExist !== []){

                $error = [
                    'error' => true,
                    'message' => "This username can't be used"
                ];

                return $this->json($error, Response::HTTP_CONFLICT);
            }

            // CHECK datas given

            $errors = $validator->validatePropertyValue($dataKid, 'username', $dataKid->getUsername());
            if ((count($errors) > 0) ){
               
                return $this->ErrorMessageNotValid($errors);

            }   

            // set
            $kid->setUsername($dataKid->getUsername());
        } 
        
        //***** CHECK PASSWORD *******

        if ($dataKid->getPassword()!== null) {

            $errors = $validator->validatePropertyValue($dataKid, 'password', $dataKid->getPassword());
            if ((count($errors) > 0)) {
 
                return $this->ErrorMessageNotValid($errors);

            }

            $password = $passwordHasher->hashPassword($dataKid, $dataKid->getPassword());
            $dataKid->setPassword($password);
            $kid->setPassword($dataKid->getPassword());
        }

        $em->persist($kid);
        $em->flush();

        return $this->prepareResponse('Successfully updated', [], [], false, Response::HTTP_OK );
    }



     /** 
     * Update a user
     * 
     * @Route("/users/{id}", name="update_user", methods="PATCH", requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function updateUser(
        $id,
        EntityManagerInterface $em, 
        UserRepository $userRepository,
        Request $request, 
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
        )
    {
        $user = $userRepository->find($id);

        // CHECK USER exists

        if ($user === null )
        {
            return $this->ErrorMessageNotFound("The user not found for id: ", $id);

        }

        $data = $request->getContent();
        $dataUser = $serializer->deserialize($data, User::class, 'json');

        // CHECK EMAIL if given

        if($dataUser->getEmail() == !null){
            $errors = $validator->validatePropertyValue($dataUser, 'email', $dataUser->getEmail());
            if ((count($errors) > 0) ){
                
                return $this->ErrorMessageNotValid($errors);

            }   
            $user->setEmail($dataUser->getEmail());
        } 

        // CHECK FIRSTNAME if given

        if($dataUser->getFirstname()!== null){
            $errors = $validator->validatePropertyValue($dataUser, 'firstname', $dataUser->getFirstname());
            if ((count($errors) > 0) ){

                return $this->ErrorMessageNotValid($errors);
            } 
            $user->setFirstname($dataUser->getFirstname());  
        }

        // CHECK LASTNAME if given

        if ($dataUser->getLastname()!== null) {
            $errors = $validator->validatePropertyValue($dataUser, 'lastname', $dataUser->getLastname());
            if ((count($errors) > 0)) {
               
                return $this->ErrorMessageNotValid($errors);

            }
            $user->setLastname($dataUser->getLastname());
        }

        // CHECK PASSWORD if given

        if ($dataUser->getPassword()!== null) {
            $errors = $validator->validatePropertyValue($dataUser, 'password', $dataUser->getPassword());
            if ((count($errors) > 0)) {
 
                return $this->ErrorMessageNotValid($errors);

            }

            $password = $passwordHasher->hashPassword($dataUser, $dataUser->getPassword());
            $dataUser->setPassword($password);
            $user->setPassword($dataUser->getPassword());
        }

        $em->persist($user);
        $em->flush();

        return $this->prepareResponse('Sucessfully updated', [], [], false, Response::HTTP_OK );
    }



     /**
     * @Route("/users/delete/{id}", name="delete_user", methods="DELETE"), requirements={"id"="\d+"}
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function delete(int $id, EntityManagerInterface $em, UserRepository $userRepository) :Response
    {

       $user = $userRepository->find($id);

        // CHECK USER exists

            if ($user === null )
            {
                return $this->ErrorMessageNotFound("The user not found for id: ", $id);

            }

        $em->remove($user);
        $em->flush();

        return $this->prepareResponse("The User has been deleted successfully",[] ,[], false, Response::HTTP_OK);
    }


    /** 
     * @Route("/users/{user_id}/kids/{kid_id}", name="delete_kid", methods="Delete")
     * @IsGranted("ROLE_USER")
     * @return Response
     */

    public function deleteKid(
        $user_id, $kid_id,
        EntityManagerInterface $em, 
        UserRepository $userRepository,
        KidRepository $kidRepository
        )
        {
            $user = $userRepository->find($user_id);
            $kid = $kidRepository->find($kid_id);

        // CHECK USER exists

           if ($user === null )
           {
            return $this->ErrorMessageNotFound("The user not found for id: ", $user_id);

           }

        // CHECK KID exists

               if ($kid === null )
           {
               return $this->ErrorMessageNotFound("The kid not found for id: ", $kid_id);
               
           }
     
        // CHECK if this kid belongs to this User

            if ($kid->getUser() !== $user){

                $error = [
                    'error'=> true,
                    'message'=> "can't delete this kid"

                ];

                return $this->json($error, Response::HTTP_NOT_FOUND);
            }



        $em->remove($kid);
        $em->flush();

        return $this->prepareResponse("The kid was succesfully deleted", [] ,[], false, Response::HTTP_OK);

        }
    /**
     * Reset Password for a User
     * 
     * @Route("/resetPassword", name="resetPassword", methods="post")
     * @return Response
     */
    public function resetPassword(UserRepository $userRepository,EntityManagerInterface $em, 
    Request $request, 
    SerializerInterface $serializer,
    UserPasswordHasherInterface $passwordHasher,
    MailerInterface $mailer

    ) :Response
    {

        $data = $request->getContent();

        $parseData = json_decode($data);
        $mailGiven = $parseData->{"email"};

        
        $user = $userRepository->findOneBy(["email"=>$mailGiven]);

        // CHECK Mail exists

        if ($user === null )
        {
            return $this->json("No message sent",200,[],[]);
        }

        // create new password

        $randomLowerLetter      = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 2, 4);
        $randomUpperLetter      = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 2, 4);
        $randomInt              = random_int(101, 999);
        $randomSpecialCharacter = substr(str_shuffle("&#{[(|-\_^@$%!?*><"), 2, 4);
            
            $passwordSend = $randomLowerLetter . $randomInt . $randomSpecialCharacter . $randomUpperLetter;

        // send new password

            $email = (new TemplatedEmail())
            ->from(new Address('livresOtresor@apotheose.com', 'Livres O Trésor'))
            ->to(new Address ($user->getEmail(), $user->getFirstName()))
            ->subject('Livres O Trésor: réinitialisation du mot de passe')
            ->htmlTemplate('email/resetPassword.html.twig')
            ->context([
                'user'=>$user,
                'password'=>$passwordSend
            ]);
            
            $mailer->send($email);

        $message = [
            'error' => false,
            'message' => "Message has been sent correctly"
        ];
        
        // Set new password
        
        $password = $passwordHasher->hashPassword($user, $passwordSend);
        $user ->setPassword($password);
        
        $em->persist($user);
        $em->flush();
        
        return $this->json($message, 200);
    }
        

    /**
     * Sent error message if not valid
     * @param mixed $errors errors found during validation
     * 
     */
    private function ErrorMessageNotValid($errors){

        if ((count($errors) > 0)) {

             /*
            * Uses a __toString method on the $errors variable which is a
            * ConstraintViolationList object. This gives us a nice string
            * for debugging.
            */
 
            $errorsString = (string) $errors;
            $error = [
                'error' => true,
                'message' => $errorsString
            ];

            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Send error message if not found
     * @param string $message error message to send if not found
     * @param int $id id
     */
    private function ErrorMessageNotFound( $messageError, $id){
        

        
            $error = [
                'error' => true,
                'message' => $messageError."[" . $id . "]"
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);         

    }

    /**
     * Manage Error message
     * @param string $message  Message to return
     * @param array  $options  
     * @param array  $data     List of object concern    
     * @param bool   $isError  If there is error or not
     * @param int    $httpCode The response status code
     * @param array  $headers  An array of reponse headers
     * 
     */
    private function prepareResponse(
        string $message, 
        array $options = [], 
        array $data = [], 
        bool $isError = false, 
        int $httpCode = 200, 
        array $headers = []
    )
    {
        $responseData = [
            'error' => $isError,
            'message' => $message,
        ];

        foreach ($data as $key => $value)
        {
            $responseData[$key] = $value;
        }
        return $this->json($responseData, $httpCode, $headers, $options);
    }

   
}
