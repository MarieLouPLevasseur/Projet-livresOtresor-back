<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/v1/apiKey", name="app_api_key")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function getApiKey($ApiIsbndbKey, $ApiGoogleKey): Response
    {
        
        $data = [
            "apiKeyGoogle" => $ApiGoogleKey,
            "apiKeyIsbndb" => $ApiIsbndbKey
        ];
               return $this->json($data,200);

    }
}
