<?php

namespace App\Controller;

use App\Entity\Kid;
use App\Entity\User;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;






/**
 * Undocumented class
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
     * @Route("/users", name="create", methods="POST")
     * 
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
     * list all users
     *
     * @Route("/users", name="list", methods="GET")
     * @return Response
     */
    public function list(UserRepository $userRepository): Response
    {

        $users = $userRepository->findAll();
        
        return $this->json($users, 200, [], ['groups' => 'user_list']);
    }

    /**
     * @Route("/users/{id}", name="show", methods="GET", requirements={"id"="\d+"})
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
     * list all kids by user
     *
     * @Route("/users/{id}/kids", name="listkids", methods="GET", requirements={"id"="\d+"})
     * @return Response
     */
    public function listkids( int $id, UserRepository $userRepository, KidRepository $kidRepository): Response
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
     * @Route("/users/{id}/kids", name="create_kid", methods="POST", requirements={"id"="\d+"})
     * @return Response
     */
    public function createKid( int $id, 
    EntityManagerInterface $em, 
    Request $request, 
    UserRepository $userRepository, 
    SerializerInterface $serializer,
    RoleRepository $roleRepository,
    UserPasswordHasherInterface $passwordHasher
    ):Response
    
    {
        $user = $userRepository->find($id);
        $kidData = $request->getcontent();
        $role = $roleRepository->findOneByRoleName("ROLE_KID");        
        $kidData = $serializer->deserialize($kidData, Kid::class, 'json');
        $password = $passwordHasher->hashPassword($user, $user->getPassword());
        $kidData ->setPassword($password);
        $kidData->setRole($role);
        $kidData->setUser($user);
        $kidData->setProfileAvatar('https://bombyxplm.com/wp-content/uploads/2021/01/421-4213053_default-avatar-icon-hd-png-download.png');
    

        if ($user === null )
        {
            $error = [
                'error' => true,
                'message' => 'No user found for Id [' . $id . ']'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
        }
          
        $em->persist($kidData);
        $em->flush();
        $this->addFlash('success', "L'enfant a bien été enregistré");

        return new Response("L'enfant a bien été enregistré");
        

    }
     /** 
     * @Route("/users/{id<\d+>}", name="update_user", methods="PATCH")
     * @return Response
     */

    public function update(
        $id,
        EntityManagerInterface $em, 
        UserRepository $userRepository,
        Request $request, 
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        RoleRepository $roleRepository,
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
     
       
   
        //$errors = $validator->validate($dataUser);
        // if (count($errors) > 0) {
        // $errorsString = (string) $errors;
        // }
        
        if($dataUser->getEmail() == !null){
            $errors = $validator->validatePropertyValue($dataUser, 'email', $dataUser->getEmail());
            if ((count($errors) > 0) ){
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
            $user->setEmail($dataUser->getEmail());
        } 

        if($dataUser->getFirstname()!== null){
            $errors = $validator->validatePropertyValue($dataUser, 'firstname', $dataUser->getFirstname());
            if ((count($errors) > 0) ){
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
            $user->setFirstname($dataUser->getFirstname());  
        }

        if ($dataUser->getLastname()!== null) {
            $errors = $validator->validatePropertyValue($dataUser, 'lastname', $dataUser->getLastname());
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
            $user->setLastname($dataUser->getLastname());
        }
        if ($dataUser->getLastname()!== null) {
            $errors = $validator->validatePropertyValue($dataUser, 'lastname', $dataUser->getLastname());
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
            $user->setLastname($dataUser->getLastname());
        }
        if ($dataUser->getPassword()!== null) {
            $errors = $validator->validatePropertyValue($dataUser, 'password', $dataUser->getPassword());
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

            $password = $passwordHasher->hashPassword($dataUser, $dataUser->getPassword());
            $dataUser->setPassword($password);
            $user->setPassword($dataUser->getPassword());
        }

        //dd($user);
        $em->persist($user);
        $em->flush();
        return $this->prepareResponse('Sucessfully updated', [], [], false, Response::HTTP_OK );
    }

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
