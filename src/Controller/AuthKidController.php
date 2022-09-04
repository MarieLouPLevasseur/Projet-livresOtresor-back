<?php

namespace App\Controller;

use Serializable;
use App\Repository\KidRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;

class AuthKidController extends AbstractController implements ServiceSubscriberInterface
{

    private $security;

     public function __construct(Security $security)
     {
         $this->security = $security;
     }


    /**
     * @Route("/api/v1/login/kid", name="app_login_kid")
     */
    public function kidLogin(Request $request,SerializerInterface $serializer,JWTTokenManagerInterface $JWTManager, KidRepository $kidRepository,UserPasswordHasherInterface $passwordHasher){

       
        // Get posted datas
        $data = $request->getContent();
        $parsed_json = json_decode($data);

        // Check if username exists
        $username = $parsed_json->{"username"};

        if (null == $username) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_BAD_REQUEST);
        }


        // $connected = $this->$jWTAuthenticator->getUserProvider();
        // $connected = $this->security->getUser();
        // $connected = $JWTManager->getUserIdClaim();
        // dd($connected);

        // Check if kid exist
        $validatedKid = $kidRepository->loadUserByIdentifier($username);

        if (!$validatedKid) {
            return $this->json([
                'message' => 'No kid found',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Check if password valid
        $passwordGiven = $parsed_json->{"password"};


        $passwordCheck = $passwordHasher->isPasswordValid($validatedKid, $passwordGiven);


        if ($passwordCheck === false) {
            return $this->json([
                'message' => 'False credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // dd($passwordCheck);

        $token =  $JWTManager->create($validatedKid);
        $jsonKidData = $serializer->serialize($validatedKid, 'json', ['groups' => 'userConnected']);


        $finalJson = '
            {
            "user" : '.$jsonKidData.' ,
            "token" : "'.$token.'"
            }
            ';

        return new JsonResponse ($finalJson, 200,[],true);

      

        // all manage by UserAuthenticator
        // return $this->json("houston...on a un probleme chez les kids",400);

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