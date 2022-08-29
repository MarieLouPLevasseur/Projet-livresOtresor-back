<?php

namespace App\Controller;

use App\Repository\BookKidRepository;
use App\Repository\BookRepository;
use App\Repository\KidRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



/**
 * List all books
 * 
 *  @Route("/api/v1", name="api_book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/books", name="book_list", methods="GET")
     * @return Response
     */
    public function listAllBooks(BookRepository $bookRepository): Response
    {

        $books = $bookRepository->findAll();
        
        return $this->prepareResponse(
            'OK',
            ['groups' => 'book_list'],
            ['data' => $books]
        );
    }

    private function prepareResponse(
        string $message, 
        array $options = [], 
        array $data = [], 
        bool $isError = false, 
        int $httpCode = 200, 
        array $headers = []
    )
    {
        $responseData = [
            'error' => $isError,
            'message' => $message,
        ];

        foreach ($data as $key => $value)
        {
            $responseData[$key] = $value;
        }
        return $this->json($responseData, $httpCode, $headers, $options);
    }
}


