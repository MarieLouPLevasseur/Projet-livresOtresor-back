<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\KidRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JetBrains\PhpStorm\Internal\ReturnTypeContract;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;





/**
 * Undocumented class
 *  @Route("/api/v1", name="api_user")
 */
class UserController extends AbstractController
{

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


        ): JsonResponse
            {

        $data = $request->getcontent();
        
        $user = $serializer->deserialize($data, User::class, 'json');

        $role = $roleRepository->findOneByRoleName("ROLE_USER");
        $user->setRole($role);

        $password = $passwordHasher->hashPassword($user, $user->getPassword());
        $user ->setPassword($password);

      
        $em->persist($user);
        $em->flush();


        return $this->json('Created', 200, [], []);

       
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


}
