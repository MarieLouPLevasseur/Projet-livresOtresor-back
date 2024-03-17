<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\AvatarRepository;
use App\Repository\DiplomaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Show users
 * 
 * @Route("/api/v1/admin", name="api_admin_")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * Show users
     * 
     * @Route("/users", name="index_users")
     */
    public function getUsers(
                                UserRepository $userRepository,
                                SerializerInterface $serializer): Response
    {
        $users = $userRepository->findAll();
    
        $jsonUsers = $serializer->serialize($users, 'json',['groups' => 'adminUsers']);

        return new JsonResponse($jsonUsers, Response::HTTP_OK, [],true);

    }

    /**
     * Show avatars
     * 
     * @Route("/avatars", name="index_avatars")
     */
    public function getAvatars(
                                AvatarRepository $avatarRepository,
                                SerializerInterface $serializer): Response
    {
        $avatars = $avatarRepository->findAll();
    
        $jsonAvatars = $serializer->serialize($avatars, 'json',['groups' => 'adminAvatars']);

        return new JsonResponse($jsonAvatars, Response::HTTP_OK, [],true);

    }

    /**
     * Show Diplomas
     * 
     * @Route("/diplomas", name="index_diplomas")
     */
    public function getDiplomas(
                                DiplomaRepository $diplomaRepository,
                                SerializerInterface $serializer): Response
    {
        $diplomas = $diplomaRepository->findAll();
    
        $jsonDiplomas = $serializer->serialize($diplomas, 'json',['groups' => 'adminDiplomas']);

        return new JsonResponse($jsonDiplomas, Response::HTTP_OK, [],true);

    }
}
