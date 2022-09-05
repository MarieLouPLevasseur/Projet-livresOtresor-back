<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Repository\KidRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;




class UserAuthenticator extends AbstractAuthenticator 
{

    private $userRepository;
    private $JWTManager;
    private $serializer;
    private $kidRepository;

    public function __construct(UserRepository $userRepository, JWTTokenManagerInterface $JWTManager, SerializerInterface $serializer, KidRepository $kidRepository)
    {
        $this->userRepository = $userRepository;
        $this->JWTManager = $JWTManager;
        $this->serializer = $serializer;
        $this->kidRepository = $kidRepository;
    }
    
    public function supports(Request $request): ?bool
    {
        return (($request->getPathInfo() === '/api/v1/login/user' || $request->getPathInfo() === '/api/v1/login/kid')  && $request->isMethod('POST'));
    }

    public function authenticate(Request $request): Passport
    {
        //****  User Connexion******

        $data=$request->getContent();
        $parsed_json = json_decode($data);


        if ($request->getPathInfo() == '/api/v1/login/user') {

            $email = $parsed_json->{"email"};

            // Check the repository to be sure to get the good Entity
            if($this->userRepository->findOneBy(['email' => $email])){
                
                $password = $parsed_json->{"password"};

                return new Passport(new UserBadge($email), new PasswordCredentials($password)); 
            } 

            // if wrong user, set to false
            return new Passport(new UserBadge("false"), new PasswordCredentials("false")); 
        }
   
        //****  Kid Connexion******

        if ($request->getPathInfo() == '/api/v1/login/kid') {
            
                $username = $parsed_json->{"username"};

            // Check the repository to be sure to get the good Entity
            if ($this->kidRepository->findOneBy(['username' => $username])) {
                    $password = $parsed_json->{"password"};

                return new Passport(new UserBadge($username), new PasswordCredentials($password));
            }

            // if wrong user, set to false
            return new Passport(new UserBadge("false"), new PasswordCredentials("false")); 
        }

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        $user = $token->getUser();
        $tokenJWT = $this->JWTManager->create($user);
     
        $jsonUserData = $this->serializer->serialize($user, 'json', ['groups' => 'userConnected']);


        $finalJson = '
            {
            "user" : '.$jsonUserData.' ,
            "token" : "'.$tokenJWT.'"
            }
            ';

        return new JsonResponse ($finalJson, 200,[],true);
        
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse("Authentification failed", 401);
    }

}
