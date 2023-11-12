<?php

// src/Controller/Api/SearchController.php

namespace App\Controller;

use App\Service\BookSearchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{

    /**
     * @Route("/api/search", name="api_search_books", methods={"POST"})
     */
    public function searchBooks(Request $request, BookSearchService $bookSearchService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $searchQuery = $data['searchQuery'];

        $books = $bookSearchService->searchBooks($searchQuery);

        $jsonData = json_encode(['books' => $books]);

        $response = new JsonResponse($jsonData, 200, [], true);

        return $response;

    }
}