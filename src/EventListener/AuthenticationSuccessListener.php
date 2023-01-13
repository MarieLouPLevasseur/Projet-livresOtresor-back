<?php
namespace App\EventListener;

use App\Entity\Kid;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;


class AuthenticationSuccessListener
{
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        if($user instanceof \App\Entity\Kid){

            $data['user'] = array(
                'id'=> $user->getId(),
                'username'=>$user->getUsername(),
                'fisrtname'=>$user->getFirstname(),
                'profil_avatar'=> $user->getProfileAvatar(),
                'roles' => $user->getRoles(),
            );
        }

        if($user instanceof \App\Entity\User){

            $data['user'] = array(
                'id'=> $user->getId(),
                'firstname'=>$user->getFirstname(),
                'lastname'=> $user->getLastname(),
                'roles' => $user->getRoles(),
            );
        }

        $event->setData($data);
    }
}