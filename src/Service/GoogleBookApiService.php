<?php

namespace App\Service;

use App\Entity\Author;
use stdClass;
use App\Entity\Book;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleBookApiService
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function searchBooks ($searchQuery){

        $baseUrl = 'https://www.googleapis.com/books/v1/volumes';
        $apiKey = $_ENV['API_GOOGLE_KEY'];

        $urlRequest = $baseUrl.'?q='.$searchQuery.'&maxResults=40'.'&langRestrict=fr'.'&key='.$apiKey;
        $responseNative = $this->client->request('GET', $urlRequest);
        $content = json_decode($responseNative->getContent());
        $response = $this->filterResponse($content->items);

        return $response;
        
    }

    function filterResponse($resultGoogleApi) {
        $indexToDelete = [];
        $hasISBN13 = false;
        foreach ($resultGoogleApi as $bookIndex => $book) {
            $hasISBN13 = false; // Indicateur pour vérifier si un ISBN_13 a été trouvé

            // Vérifie si la clé existe
            if (isset($book->volumeInfo) && isset($book->volumeInfo->industryIdentifiers)) {
                // Parcourez les identifiants de l'industrie
                foreach ($book->volumeInfo->industryIdentifiers as $identifier) {
                    // Vérifiez si le type est ISBN_13
                    if ($identifier->type === 'ISBN_13') {
                        $hasISBN13 = true;
                    }
                }
            } else {
                // Ajoutez l'index à la liste des éléments à supprimer
                $indexToDelete[] = $bookIndex;
            }

            if (!$hasISBN13) {
                $indexToDelete[] = $bookIndex;
            }
        }

        // Supprime les éléments de la liste
        foreach ($indexToDelete as $index) {
            unset($resultGoogleApi[$index]);
        }
    
      
        // Réindexe le tableau pour éviter les indices manquants
        $resultGoogleApi = array_values($resultGoogleApi);
      
        $formatedResponse = $this->formatGoogleBookData($resultGoogleApi);
        return $formatedResponse;

    }

    public function formatGoogleBookData($googleBookData)
    {
        $formattedBooks = [];

        foreach ($googleBookData as $item) {
            $book = new Book(); 
            // $book = new stdClass(); 

            // Récupérer l'ISBN-13 s'il existe
            $isbn13 = null;
            foreach ($item->volumeInfo->industryIdentifiers as $identifier) {
                if ($identifier->type === 'ISBN_13') {
                    $isbn13 = $identifier->identifier;
                    break;
                }
            }

            // Si l'ISBN-13 est trouvé, formater les données du livre
            if ($isbn13) {
                $book->setIsbn($isbn13);
                $book->setTitle($item->volumeInfo->title);
                if (isset($item->volumeInfo->subtitle)) {
                    $book->setTitle($item->volumeInfo->subtitle .= ' ' . $item->volumeInfo->subtitle);
                }
                $book->setDescription($item->volumeInfo->description ?? '');
                $book->setPublisher($item->volumeInfo->publisher ?? '');
                $authors = new Author;
           
                if (isset($item->volumeInfo->authors)) {
                    foreach ($item->volumeInfo->authors as $author) {

                        $authors->setName($author);
                    }
                }
            
                $book->addAuthor($authors);

                $book->setCover ($item->volumeInfo->imageLinks->thumbnail ?? ''); // TODO: vérifier qu'il n'y ait pas de casse 

                $formattedBooks[] = $book;
            }
        }
       
        return $formattedBooks;
    }

   
}