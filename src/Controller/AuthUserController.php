<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthUserController extends AbstractController
{
    /**
     * @Route("/api/v1/login/user", name="app_login_user")
     */
    public function userLogin(AuthenticationUtils $authenticationUtils)//: Response
    {
        // all manage by UserAuthenticator

    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        // throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}