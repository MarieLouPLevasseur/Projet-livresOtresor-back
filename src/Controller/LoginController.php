<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends AbstractController
{
    /**
     * @Route("/login/user", name="app_login", methods="POST")
     */
    public function login(
        AuthenticationUtils $authenticationUtils,
        SerializerInterface $serializer,
        Request $request
    ): Response
    {
       
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'error' => 'Invalid login request: check that the Content-Type header is "application/json".'
            ], 401);
        }

        $userConnected= $this->getUser();
        $jsonUserInfos = $serializer->serialize($userConnected, 'json',['groups' => 'userConnected']);

        $session= $request->getSession();
        $token = $session->get('token');

        $bearer = $request->headers->get('Authorization');
        $accessToken = substr($bearer, 7);
      
       return new JsonResponse($jsonUserInfos,Response::HTTP_OK, [],true );
    }

     /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
       
    }
}
