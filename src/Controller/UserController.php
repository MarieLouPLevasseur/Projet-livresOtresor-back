<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\KidRepository;
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
<<<<<<< HEAD
     * @Route("/user/{id}", name="show", methods="GET", requirements={"id"="\d+"})
=======
     * @Route("/users/{id}", name="show", methods="GET", requirements={"id"="\d+"})
>>>>>>> 2ad2b35fd9296a52b787dd8b53a2e0336e17d1be
     * @return Response
     */
    public function show(int $id, UserRepository $userRepository) :Response
    {
 
        $user = $userRepository->find($id);
        if ($user === null )
        {
<<<<<<< HEAD
            // if the user doesn't  exist, display an error message.
=======

            // if the user doesn't  exist, display an error message.

>>>>>>> 2ad2b35fd9296a52b787dd8b53a2e0336e17d1be
            $error = [
                'error' => true,
                'message' => 'No user found for Id [' . $id . ']'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
        }
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user_list']);
<<<<<<< HEAD
=======
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
>>>>>>> 2ad2b35fd9296a52b787dd8b53a2e0336e17d1be
    }
}
