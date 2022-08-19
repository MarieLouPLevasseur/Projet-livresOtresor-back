<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\KidRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;



/**
 * Undocumented class
 *  @Route("/api/v1", name="api_book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/books", name="list", methods="GET")
     * @return Response
     */
    public function list(BookRepository $bookRepository): Response
    {

        $books = $bookRepository->findAll();
        
        return $this->prepareResponse(
            'OK',
            ['groups' => 'book_list'],
            ['data' => $books]
        );
    }

    /**
     * @Route("/kid/{id}/books", name="show", methods="GET", requirements={"id"="\d+"})
     * @return Response
     */
    public function showBookOfOneKid( int $id, KidRepository $kidRepository, bookRepository $bookRepository): Response

    {
        $kid = $kidRepository->find($id);
        //$bookRead = $bookRepository-> findAll();
        


        if ($kid === null )
        {
            
            $error = [
                'error' => true,
                'message' => 'No kid found for Id [' . $id . ']'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
        }

        $bookKid = $kid->getBookKids();
    
        //$jsonBooksKidList = $serializer->serialize($bookskids, 'json',['groups' => 'booksKid']);

        return $this->prepareResponse(
            'OK',
            ['groups' => 'books_infos'],
            ['data' => $bookKid]
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
