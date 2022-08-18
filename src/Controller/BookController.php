<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Undocumented class
 *  @Route("/api_v1", name="api_book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/books", name="list", methods="GET")
     * @return Response
     */
    public function list(BookRepository $bookRepository): Response
    {

        $book = $bookRepository->findAll();
        
        return $this->json($book, 200, [], ['groups' => 'book_list']);
    }

    /**
     * @Route("/book/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     * @return Response
     */
    public function show(int $id, bookRepository $bookRepository): Response

    {
        $book = $bookRepository->find($id);
        if ($book === null )
        {
        
            $error = [
                'error' => true,
                'message' => 'No book found for Id [' . $id . ']'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
        }
        return $this->json($book, Response::HTTP_OK, [], ['groups' => 'book_list']);
    }
}
