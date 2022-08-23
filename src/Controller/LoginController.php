<?php

namespace App\Controller;

use App\Entity\User as EntityUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


    class LoginController extends AbstractController
{
    /**
     * @Route("/api/v1/login/user", name="app_login_user", methods="POST")
     */
    public function login(
        AuthenticationUtils $authenticationUtils,
        SerializerInterface $serializer,
        Request $request
        // InMemoryUser $user
            ): Response
    {


            //  if (null === $user) {
            //     // if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {

            //     return $this->json([
            //         'message' => 'missing credentials',
            //     ], Response::HTTP_UNAUTHORIZED);
            // }
           
            //         // $token = ...; // somehow create an API token for $user
           
            //          return $this->json([
            //             'message' => 'Welcome to your new controller!',
            //             'path' => 'src/Controller/ApiLoginController.php',
            //             'user'  => $user->getUserIdentifier(),
            //             // 'token' => $token,
            //           ]);




       
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'error' => 'Invalid login request: check that the Content-Type header is "application/json".'
            ], 401);
        }

        $userConnected= $this->getUser();
        $jsonUserInfos = $serializer->serialize($userConnected, 'json',['groups' => 'userConnected']);

        // $session= $request->getSession();
        // $token = $session->get('token');

        // $bearer = $request->headers->get('Authorization');
        // $accessToken = substr($bearer, 7);
      
       return new JsonResponse($jsonUserInfos,Response::HTTP_OK, [],true );
    }

     /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
       
    }
}
