<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * List all books
 * 
 *  @Route("/api/v1", name="api_book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/books/search", name="search", methods="POST")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */
    public function SearchBooks(Request $request,BookRepository $bookRepository): Response
    {

        $data = $request->getContent();
        $parsed_json = json_decode($data);

        $query = $parsed_json->{"search"};
        // dd($query);
    

        // dd($request);
        if($query) {
            $books = $bookRepository->findBooksbyTitle($query);
        }

        // dd($books);

        return $this->prepareResponse(
            'OK',
            ['groups' => 'book_list'],
            ['data' => $books]
        );

    }

    /**
     * @Route("/books", name="book_list", methods="GET")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
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


