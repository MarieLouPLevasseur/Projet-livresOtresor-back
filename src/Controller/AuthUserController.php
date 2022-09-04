<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthUserController extends AbstractController
{
    /**
     * @Route("/api/v1/login/user", name="app_login_user")
     */
    public function userLogin(Request $request,SerializerInterface $serializer,JWTTokenManagerInterface $JWTManager, UserRepository $userRepository,UserPasswordHasherInterface $passwordHasher)//: Response
    {


         // Get posted datas
         $data = $request->getContent();
         $parsed_json = json_decode($data);
 
         // Check if email exists
         $email = $parsed_json->{"email"};
 
         if (null == $email) {
             return $this->json([
                 'message' => 'missing credentials',
             ], Response::HTTP_BAD_REQUEST);
         }

 
         // Check if kid exist
         $validatedUser = $userRepository->loadUserByIdentifier($email);
 
         if (!$validatedUser) {
             return $this->json([
                 'message' => 'No kid found',
             ], Response::HTTP_BAD_REQUEST);
         }
 
         // Check if password valid
         $passwordGiven = $parsed_json->{"password"};
 
 
         $passwordCheck = $passwordHasher->isPasswordValid($validatedUser, $passwordGiven);
 
 
         if ($passwordCheck === false) {
             return $this->json([
                 'message' => 'False credentials',
             ], Response::HTTP_UNAUTHORIZED);
         }
 
         // dd($passwordCheck);
 
         $token =  $JWTManager->create($validatedUser);
         $jsonUserData = $serializer->serialize($validatedUser, 'json', ['groups' => 'userConnected']);
 
 
         $finalJson = '
             {
             "user" : '.$jsonUserData.' ,
             "token" : "'.$token.'"
             }
             ';
 
         return new JsonResponse ($finalJson, 200,[],true);
        // all manage by UserAuthenticator
        // return $this->json("houston...on a un probleme chez les users",400);

    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        // throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        return $this->json("successfully disconnected", 200);

    }
}