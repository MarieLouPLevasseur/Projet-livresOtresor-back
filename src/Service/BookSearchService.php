<?php

namespace App\Service;

use App\ExternalApi\IsbnDbApi;
use App\ExternalApi\GoogleBooksApi;
use App\ExternalApi\OpenLibraryApi;
use App\Service\GoogleBookApiService;
use App\Service\OpenLibraryApiService;

class BookSearchService
{
    private $googleBooksApi;
    private $openLibraryApi;

    public function __construct(GoogleBookApiService $googleBooksApi, OpenLibraryApiService $openLibraryApi)
    {
        $this->googleBooksApi = $googleBooksApi;
        $this->openLibraryApi = $openLibraryApi;
    }

    public function searchBooks(string $searchQuery): array
    {
        $googleBooksResult = $this->googleBooksApi->searchBooks($searchQuery);
        $validIsbnList = $this->getValidISBNList($googleBooksResult);
      
        $openLibraryResult = $this->openLibraryApi->searchBooks($validIsbnList);
      
        // Traitez les résultats et renvoyez la liste complète de livres
        $completeBookList = $this->getCompleteBooks($googleBooksResult, $openLibraryResult);

        return $completeBookList;
    }

    private function getCompleteBooks(array $googleBooksResult, array $openLibraryResult): array
    {
        $completeBookList = [];
    
        foreach ($googleBooksResult as $googleBook) {
          
            $authors = array_map(function ($author) {
                return ["name" => $author->getName()];
            }, $googleBook->getAuthors()->toArray());

            $mergedAuthors = call_user_func_array('array_merge', $authors);

            $completeBook = [
                'id' => $googleBook->getId(),
                'isbn' => $googleBook->getIsbn(),
                'title' => $googleBook->getTitle(),
                'description' => $googleBook->getDescription(),
                'publisher' => $googleBook->getPublisher(),
                'cover' => $googleBook->getCover(),
                'authors' => [$mergedAuthors],
            ];

            // Vérifiez si les propriétés requises sont vides
            if (empty($completeBook['title']) || empty($completeBook['description']) || empty($completeBook['publisher'] || empty($completeBook['authors']))) {
                $isbn = $googleBook->getIsbn();
                
                // Recherchez dans les objets OpenLibrary correspondants
                foreach ($openLibraryResult as $openLibraryBook) {
                    if ($openLibraryBook->getIsbn() === $isbn) {
                        // Remplacez les propriétés vides par celles de l'OpenLibrary Book
                        if (empty($completeBook['title']) && !empty($openLibraryBook->getTitle())) {
                            $completeBook['title'] = $openLibraryBook->getTitle();
                        }
                        
                        if (empty($completeBook['description']) && !empty($openLibraryBook->getDescription())) {
                            $completeBook['description'] = $openLibraryBook->getDescription();
                        }
                        
                        if (empty($completeBook['publisher']) && !empty($openLibraryBook->getPublisher())) {
                            $completeBook['publisher'] = $openLibraryBook->getPublisher();
                        }
                        if (empty($completeBook['authors']) && !empty($openLibraryBook->getAuthors())) {

                            $authors = array_map(function ($author) {
                                    return $author;
                                }, $openLibraryBook->getAuthors()->toArray());
                                $mergedAuthors = call_user_func_array('array_merge', $authors);

                            $completeBook['authors'] = $mergedAuthors;
                        }
                                                
                        break;
                    }
                }
            }

            $completeBookList[] = $completeBook;
        }
        
        return $completeBookList;
    }


    function getValidISBNList($resultGoogleApi) {

        $ISBNList = [];
      
        foreach ($resultGoogleApi as $book) {

            $ISBNList[] = $book->getIsbn();           
        }

        return $ISBNList;
    }

}
