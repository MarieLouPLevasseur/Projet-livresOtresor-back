<?php

namespace App\Controller;

use App\Entity\User as EntityUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;


    class LoginController extends AbstractController
{
    /**
     * @Route("/api/v1/login/user", name="app_login_user", methods="POST")
     */
    public function login(
        AuthenticationUtils $authenticationUtils,
        SerializerInterface $serializer,
        Request $request,
        JWTTokenManagerInterface $JWTManager
        // InMemoryUser $user
            ): Response
    { 
       
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'error' => 'Invalid login request: check that the Content-Type header is "application/json".'
            ], 401);
        }

        $userConnected= $this->getUser();
        $jsonUserInfos = $serializer->serialize($userConnected, 'json',['groups' => 'userConnected']);

        $token =  $JWTManager->create($userConnected);

        $datas = ['user'=>$jsonUserInfos,'token'=>$token];
      
        return $this->json($datas, 201);
    }

     /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
       
    }
}
