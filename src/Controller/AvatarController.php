<?php

namespace App\Controller;

use App\Repository\AvatarRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AvatarController extends AbstractController
{
    /**
<<<<<<< HEAD
     * @Route("/api/v1/avatar/{id}", name="api_avatars", requirements={"id_kid"="\d+"})
=======
     * @Route("/api/v1/avatars/{id}", name="api_avatars", requirements={"id_kid"="\d+"})
>>>>>>> 2ad2b35fd9296a52b787dd8b53a2e0336e17d1be
     */
    public function show(
    int $id,
    AvatarRepository $avatarRepository,
    SerializerInterface $serializer): Response
    {
       $avatar = $avatarRepository->find($id);


        if ($avatar === null )
        {
        $error = [
            'error' => true,
            'message' => 'No avatar found for Id [' . $id . ']'
        ];

        return $this->json($error, Response::HTTP_NOT_FOUND); // page 404
        }
    
        $jsonAvatarsShow = $serializer->serialize($avatar, 'json',['groups' => 'KidAvatar']);


        return new JsonResponse($jsonAvatarsShow, Response::HTTP_OK, [],true);

    }
}
