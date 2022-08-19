<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JetBrains\PhpStorm\Internal\ReturnTypeContract;


/**
 * Undocumented class
 *  @Route("/api/v1", name="api_user")
 */
class UserController extends AbstractController
{
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
}
