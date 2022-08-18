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
 *  @Route("/api/users", name="api_user")
 */
class UserController extends AbstractController
{
    /**
     * list all users
     *
     * @Route("", name="list", methods="GET")
     * @return Response
     */
    public function list(UserRepository $userRepository): Response
    {

        $users = $userRepository->findAll();
        
        return $this->json($users, 200, [], ['groups' => 'user_list']);
    }
}
