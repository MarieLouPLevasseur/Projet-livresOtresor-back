<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class UserAuthenticator extends AbstractAuthenticator // implements UserInterface
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function supports(Request $request): ?bool
    {
        return ($request->getPathInfo() === '/api/v1/login/user' || $request->getPathInfo() === '/api/v1/login/kid'  && $request->isMethod('POST'));
    }

    public function authenticate(Request $request): Passport
    {
        // User Connexion
        $data=$request->getContent();
        $parsed_json = json_decode($data);


        if ($request->getPathInfo() == '/api/v1/login/user') {
            
            $email = $parsed_json->{"email"};
            $password = $parsed_json->{"password"};

            return new Passport(new UserBadge($email), new PasswordCredentials($password));  
        }
   
        // Kid Connexion
        if ($request->getPathInfo() == '/api/v1/login/kid') {
            
                $username = $parsed_json->{"username"};
                $password = $parsed_json->{"password"};
            
            return new Passport(new UserBadge($username), new PasswordCredentials($password));

        }

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        dd("authentification ok");
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        dd("authentification failed");
    }

}
