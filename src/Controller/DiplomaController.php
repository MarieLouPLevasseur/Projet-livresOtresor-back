<?php

namespace App\Controller;

use App\Repository\DiplomaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DiplomaController extends AbstractController
{
    /**
     * @Route("/api/v1/diplomas/{id}", name="api_diplomas", requirements={"id_kid"="\d+"})
     */
    public function show(
    int $id,
    DiplomaRepository $diplomaRepository,
    SerializerInterface $serializer): Response
    {
       $diploma = $diplomaRepository->find($id);


        if ($diploma === null )
        {
        $error = [
            'error' => true,
            'message' => 'No diploma found for Id [' . $id . ']'
        ];

        return $this->json($error, Response::HTTP_NOT_FOUND); // page 404
        }
    
        $jsonDiplomasShow = $serializer->serialize($diploma, 'json',['groups' => 'KidDiploma']);


        return new JsonResponse($jsonDiplomasShow, Response::HTTP_OK, [],true);

    }
}