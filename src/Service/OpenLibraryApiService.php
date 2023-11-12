<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\Author;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class OpenLibraryApiService
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function searchBooks ($isbnList){

        $openLibraryApiResult=[];

        foreach($isbnList as $isbn) {

         $baseUrl = 'https://openlibrary.org/api/books';
 
         $urlRequest = $baseUrl.'?bibkeys=ISBN:'.$isbn.'&jscmd=data&format=json';
 
         
         $responseNative = $this->client->request('GET', $urlRequest);
         $content = json_decode($responseNative->getContent());

         $responseFormatted = $this->formatOpenLibraryBookData($content);
 
         $openLibraryApiResult[] = $responseFormatted;
        }

        // Supprimez les éléments vides du tableau
        $formattedBooks = array_filter($openLibraryApiResult);

        // Fusionnez les éléments du tableau
        $formattedBooks = array_merge([], ...$formattedBooks);


        return $formattedBooks;

    }

    public function formatOpenLibraryBookData($openLibraryBookData)
    {

        $formattedBooks = [];
      
        foreach ($openLibraryBookData as $key => $bookData) {
            $isbn = str_replace('ISBN:', '', $key); // Récupérer l'ISBN en supprimant le préfixe 'ISBN:'
            $book = new Book(); 

            $book->setIsbn($isbn);
            $book->setTitle($bookData->title);
            $book->setDescription($bookData->description ?? '');
            $book->setPublisher($bookData->publishers[0]->name ?? ''); // Premier éditeur s'il existe
            $authors = new Author();

            if (isset($bookData->authors)) {
                foreach ($bookData->authors as $author) {
                    $authors->setName($author->name);
                }
            }

            $book->addAuthor($authors);

            $cover = '';

                if (isset($bookData->cover)) {
                    if (isset($bookData->cover->large)) {
                        $cover = $bookData->cover->large;
                    } elseif (isset($bookData->cover->medium)) {
                        $cover = $bookData->cover->medium;
                    } elseif (isset($bookData->cover->small)) {
                        $cover = $bookData->cover->small;
                    }
                }
                
            $book->setCover($cover); // TODO à vérifier 

            $formattedBooks[] = $book;
        }

        return $formattedBooks;
    }
    
}