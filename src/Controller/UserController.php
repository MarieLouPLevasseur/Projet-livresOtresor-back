<?php

namespace App\Controller;

use App\Entity\Kid;
use App\Entity\User;
use App\Repository\AvatarRepository;
use App\Repository\KidRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\VarDumper\Cloner\Data;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


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
    public function addUser( 
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        RoleRepository $roleRepository
        ): Response
    {

        $data = $request->getcontent();
        
        $user = $serializer->deserialize($data, User::class, 'json');


        $errors = $validator->validate($user);


        if (count($errors) > 0) {
            /*
            * Uses a __toString method on the $errors variable which is a
            * ConstraintViolationList object. This gives us a nice string
            * for debugging.
            */
            $errorsStringBook = (string) $errors;

            $error = [
                'error' => true,
                'message' => $errorsStringBook
            ];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

            $role = $roleRepository->findOneByRoleName("ROLE_USER");
            $user->setRole($role);
    
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user ->setPassword($password);
    
          
            $em->persist($user);
            $em->flush();

            $error = [
                'error' => false,
                'message' => "L'utilisateur a bien été enregistré"
            ];
            return $this->json($error, 201);
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
        if ($user === null )
        {

            // if the user doesn't  exist, display an error message.

            $error = [
                'error' => true,
                'message' => 'No user found for Id [' . $id . ']'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
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

        if ($user === null )
        {
            $error = [
                'error' => true,
                'message' => 'No user found for Id [' . $id . ']'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
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
       
        if ($user === null )
        {
            $error = [
                'error' => true,
                'message' => 'No user found for Id [' . $id . ']'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
        }

        $errors = $validator->validate($kidData);

        if (count($errors) > 0) {
          
            $errorsStringBook = (string) $errors;

            $error = [
                'error' => true,
                'message' => $errorsStringBook
            ];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

        $password = $passwordHasher->hashPassword($kidData, $kidData->getPassword());
        $kidData->setPassword($password);

        $em->persist($kidData);
        $em->flush();

        return $this->json("L'enfant a bien été enregistré", Response::HTTP_OK);        

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
                $error = [
                    'error' => true,
                    'message' => 'No user found for Id [' . $id_user . ']'
                ];
                return $this->json($error, Response::HTTP_NOT_FOUND);
            }

        // CHECK KID exists

            if ($kid === null )
            {
                $error = [
                    'error' => true,
                    'message' => 'No kid found for Id [' . $id_kid . ']'
                ];
                return $this->json($error, Response::HTTP_NOT_FOUND);
            }

        $data = $request->getContent();
        $dataKid = $serializer->deserialize($data, Kid::class, 'json');

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
               
                $errorsString = (string) $errors;
                $error = [
                    'error' => true,
                    'message' => $errorsString
                ];

                return $this->json($error, Response::HTTP_BAD_REQUEST);
            }   

            // set
            $kid->setUsername($dataKid->getUsername());
        } 
        
        //***** CHECK PASSWORD *******

        if ($dataKid->getPassword()!== null) {

            $errors = $validator->validatePropertyValue($dataKid, 'password', $dataKid->getPassword());
            if ((count($errors) > 0)) {
 
                $errorsString = (string) $errors;
                $error = [
                    'error' => true,
                    'message' => $errorsString
                ];

                return $this->json($error, Response::HTTP_BAD_REQUEST);
            }

            $password = $passwordHasher->hashPassword($dataKid, $dataKid->getPassword());
            $dataKid->setPassword($password);
            $kid->setPassword($dataKid->getPassword());
        }

        $em->persist($kid);
        $em->flush();
        return $this->prepareResponse('Sucessfully updated', [], [], false, Response::HTTP_OK );
    }



     /** 
     * Update a user
     * 
     * @Route("/users/{id}", name="update_user", methods="PATCH", requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function update(
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

        if ($user === null )
        {
            $error = [
                'error' => true,
                'message' => 'No user found for Id [' . $id . ']'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
        }

        $data = $request->getContent();
        $dataUser = $serializer->deserialize($data, User::class, 'json');


        if($dataUser->getEmail() == !null){
            $errors = $validator->validatePropertyValue($dataUser, 'email', $dataUser->getEmail());
            if ((count($errors) > 0) ){
               
                $errorsString = (string) $errors;
                $error = [
                    'error' => true,
                    'message' => $errorsString
                ];

                return $this->json($error, Response::HTTP_BAD_REQUEST);
            }   
            $user->setEmail($dataUser->getEmail());
        } 

        if($dataUser->getFirstname()!== null){
            $errors = $validator->validatePropertyValue($dataUser, 'firstname', $dataUser->getFirstname());
            if ((count($errors) > 0) ){
               
                $errorsString = (string) $errors;
                $error = [
                    'error' => true,
                    'message' => $errorsString
                ];

                return $this->json($error, Response::HTTP_BAD_REQUEST);
            } 
            $user->setFirstname($dataUser->getFirstname());  
        }

        if ($dataUser->getLastname()!== null) {
            $errors = $validator->validatePropertyValue($dataUser, 'lastname', $dataUser->getLastname());
            if ((count($errors) > 0)) {
               
                $errorsString = (string) $errors;
                $error = [
                    'error' => true,
                    'message' => $errorsString
                ];

                return $this->json($error, Response::HTTP_BAD_REQUEST);
            }
            $user->setLastname($dataUser->getLastname());
        }
        if ($dataUser->getLastname()!== null) {
            $errors = $validator->validatePropertyValue($dataUser, 'lastname', $dataUser->getLastname());
            if ((count($errors) > 0)) {

                $errorsString = (string) $errors;
                $error = [
                    'error' => true,
                    'message' => $errorsString
                ];


                return $this->json($error, Response::HTTP_BAD_REQUEST);
            }
            $user->setLastname($dataUser->getLastname());
        }
        if ($dataUser->getPassword()!== null) {
            $errors = $validator->validatePropertyValue($dataUser, 'password', $dataUser->getPassword());
            if ((count($errors) > 0)) {
 
                $errorsString = (string) $errors;
                $error = [
                    'error' => true,
                    'message' => $errorsString
                ];

                return $this->json($error, Response::HTTP_BAD_REQUEST);
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
     * @Route("/users/delete/{id<\d+>}", name="delete_user", methods="DELETE")
     * @return Response
     */
    public function delete(int $id, EntityManagerInterface $em, UserRepository $userRepository) :Response
    {

       $user = $userRepository->find($id);
        if ($user === null )
        {
            $error = [
                'error' => true,
                'message' => 'No user found for Id [' . $id . ']'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
        }

        $em->remove($user);
        $em->flush();

        return $this->prepareResponse("L'utilisateur supprimé avec succès",[] ,[], false, Response::HTTP_OK);
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
           // dd($user);
           if ($user === null )
           {
               $error = [
                   'error' => true,
                   'message' => 'No user found for Id [' . $user_id . ']'
               ];
               return $this->json($error, Response::HTTP_NOT_FOUND);
           }
           
               if ($kid === null )
           {
               $error = [
                   'error' => true,
                   'message' => 'No kid found for Id [' . $kid_id . ']'
               ];
               return $this->json($error, Response::HTTP_NOT_FOUND);
           }


            if ($kid->getUser() !== $user){

                $error = [
                    'error'=> true,
                    'message'=> "can't delete this kid"

                ];

                return $this->json($error, Response::HTTP_NOT_FOUND);
            }



        $em->remove($kid);
        $em->flush();
        return $this->prepareResponse("L'enfant a été supprimé avec succès", [] ,[], false, Response::HTTP_OK);

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
