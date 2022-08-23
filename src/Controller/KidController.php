<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookKid;
use App\Repository\KidRepository;
use App\Repository\BookRepository;
use App\Repository\AvatarRepository;
use App\Repository\BookKidRepository;
use App\Repository\DiplomaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;

/**
 * Kid class
 * @Route("/api/v1/kids", name="api_kids_")
 */
class KidController extends AbstractController
{
    
   

     /**
     * Show all books of a category for a kid
     * @Route("/{id_kid}/category/{id_cat}/books", name="show_category_books", methods="GET", requirements={"id_kid"="\d+"}, requirements={"id_cat"="\d+"})
     * 
     */
    public function showBooksbyCategory(
        int $id_kid,
        int $id_cat,
        BookKidRepository $bookKidRepository,
        SerializerInterface $serializer
       )//: Response
    {

        // $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        $allBooksByCategory = $bookKidRepository->findAllByKidAndCategory($id_kid, $id_cat);

        // $normalizer = new ObjectNormalizer($classMetadataFactory);
        // $serializer = new Serializer([$normalizer]);

       
        $jsonBooksCategoryList = $serializer->serialize($allBooksByCategory, 'json',['groups' => 'booksByCategory']);


        return new JsonResponse($jsonBooksCategoryList, Response::HTTP_OK, [],true);
    }

    /**
     * Show all avatars of a kid
     * @Route("/{id_kid}/avatars", name="show_avatars", methods="GET", requirements={"id_kid"="\d+"})
     * 
     */
    public function showAllAvatars(
        int $id_kid,
        KidRepository $kidRepository,
        AvatarRepository $avatarRepository,
        BookKidRepository $bookKidRepository,
        SerializerInterface $serializer
       ): Response
    {
        $currentKid = $kidRepository->find($id_kid);



        if ($currentKid === null )
        {

            $error = [
                'error' => true,
                'message' => 'No Kid found for Id [' . $id_kid . ']'
            ];

            return $this->json($error, Response::HTTP_NOT_FOUND); 
        }

        // count books

        $currentReadbooks = $bookKidRepository->findAllByIsRead(true,$id_kid);
        $totalBooksRead = count($currentReadbooks);

        // check if totalBooksRead < or = to 'is_win' and set those
        $currentAvatarsWon = $avatarRepository->findAllByIsWinValue($totalBooksRead);

        foreach($currentAvatarsWon as $avatar){

            $currentKid->addAvatar($avatar);
        }

        $currentKidAvatars = $currentKid->getAvatar();      
        $jsonAvatarsList = $serializer->serialize($currentKidAvatars, 'json',['groups' => 'KidAvatar']);


        return new JsonResponse($jsonAvatarsList, Response::HTTP_OK, [],true);
    }

     /**
     * Show all diplomas of a kid
     * @Route("/{id_kid}/diplomas", name="show_diplomas", methods="GET", requirements={"id_kid"="\d+"})
     * 
     */
    public function showAllDiplomas(
        int $id_kid,
        KidRepository $kidRepository,
        DiplomaRepository $diplomaRepository,
        BookKidRepository $bookKidRepository,
        SerializerInterface $serializer
       ): Response
    {
        $currentKid = $kidRepository->find($id_kid);



        if ($currentKid === null )
        {

            $error = [
                'error' => true,
                'message' => 'No Kid found for Id [' . $id_kid . ']'
            ];

            return $this->json($error, Response::HTTP_NOT_FOUND); 
        }

        // count books

        $currentReadbooks = $bookKidRepository->findAllByIsRead(true,$id_kid);
        $totalBooksRead = count($currentReadbooks);


        // check if totalBooks < or = to 'is_win' and set those
        $currentDiplomasWon = $diplomaRepository->findAllByIsWinValue($totalBooksRead);

        foreach($currentDiplomasWon as $diploma){

            $currentKid->addDiploma($diploma);
        }

        $currentDiplomas = $currentKid->getDiploma();      
        $jsonDiplomasList = $serializer->serialize($currentDiplomas, 'json',['groups' => 'KidDiploma']);


        return new JsonResponse($jsonDiplomasList, Response::HTTP_OK, [],true);
    }



    /**
     * @Route("/{id_kid}/books/{id_book}", name="show_book_details", methods="GET", requirements={"id_kid"="\d+"}, requirements={"id_book"="\d+"})
     * @return Response
     */
    public function showOneBookDetails( 
        int $id_kid,
        int $id_book,
        KidRepository $kidRepository,
        BookKidRepository $bookKidRepository,
        BookRepository $bookRepository,
        SerializerInterface $serializer
        ): Response

        
    {

        $currentkid = $kidRepository->find($id_kid);
        $currentBook = $bookRepository->find($id_book);

        // Find specific book
        $currentBookKid = $bookKidRepository->findOneByKidandBook($id_kid, $id_book);

        // catch errors
        if ($currentkid === null )
        {
            
            $error = [
                'error' => true,
                'message' => 'No kid found for Id [' . $id_kid . ']'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
        }

        if ($currentBook === null )
        {
            
            $error = [
                'error' => true,
                'message' => 'No book found for Id [' . $id_book . ']'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
        }

        if ($currentBookKid === [] )
        {
            
            $error = [
                'error' => true,
                'message' => 'No book found for this request'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
        }

   
        $jsonBookShow = $serializer->serialize($currentBookKid, 'json',['groups' => 'books_infos']);

        return new JsonResponse($jsonBookShow, Response::HTTP_OK, [],true);

    }

    /**
     * @Route("/{id_kid}/books", name="create_book", methods="POST", requirements={"id_kid"="\d+"})
     * @return Response
     */
    public function createBook( 
        int $id_kid,
        KidRepository $kidRepository,
        BookKidRepository $bookKidRepository,
        BookRepository $bookRepository,
        SerializerInterface $serializer,
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $em


        )//: Response

        
    {
        // recuperation des données
        $data = $request->getcontent();
        
        $book = $serializer->deserialize($data, Book::class, 'json');
        $newBookKid = $serializer->deserialize($data, BookKid::class, 'json');


        $kid = $kidRepository->find($id_kid);

        $errors = $validator->validate($book);


        // Si les données sont incomplètes ou vides je le signale par une erreur
            // données obligatoire: 
                // titre, description, auteur, is_read, ISBN
            // données facultatives:
                // publisher

        if (count($errors) > 0) {
            /*
            * Uses a __toString method on the $errors variable which is a
            * ConstraintViolationList object. This gives us a nice string
            * for debugging.
            */
            $errorsString = (string) $errors;

            return new Response($errorsString,400,[],Response::HTTP_BAD_REQUEST);
        }

        // persister les authors
            $authors= $book->getAuthors();
            foreach($authors as $author){
                $em->persist($author);
            }
        // je dois récupérer l'isbn 
            $isbnGiven = $book->getIsbn();

        // Je regarder si un objet correspond dans Book (avec requete)

            $isbnExistingInBook = $bookRepository->findOneByIsbnCode($isbnGiven);


        if ($isbnExistingInBook === null){
            // si l'objet n'existe pas, je le créer en totalité dans un nouvelle objet book à partir des données transmises
            
            // création de la nouvelle liaison Bookkid

            $newBookKid->setKid($kid);

            $book-> addBookKid($newBookKid);
            $em->persist($book);
            $em->persist($newBookKid);


        }
        else{
            // ! il faudra trier que l'enfant ne possède pas déjà l'ISBN dans sa propre table pour ne pas doubler les relations inutilement
            // Si l'object correspond, je le recupere et affecte les relations
                    // affection des obligatoires transmises
                    // role en automatique à la création via construct?
                    // affection d'un bookid ou je stocke le is_read transmis

        // création de la nouvelle liaison Bookkid
        
            // $newBookkid = new BookKid ();
            // $newBookkid->setIsRead();
            $newBookKid->setKid($kid);

            $em->persist($newBookKid);

            $isbnExistingInBook->addBookKid($newBookKid);

            $em->persist($isbnExistingInBook);

        }
        
    
          
            $em->flush();
            // $this->addFlash('success', "Le livre a bien été créer");
        // je retourne l'information de la bonne création du livres

            return $this->json("Le livre a bien été créé", 200);
    }


     /*************************Routes coded using the prepare response method*******************************************************************/

      /**
     * @Route("/{id_kid}/books", name="show_book_list", methods="GET", requirements={"id"="\d+"})
     * @return Response
     */
    public function showBookOfOneKid( int $id_kid, KidRepository $kidRepository, BookRepository $bookRepository): Response

    {
        $kid = $kidRepository->find($id_kid);

        if ($kid === null )
        {

            $error = [
                'error' => true,
                'message' => 'No kid found for Id [' .$id_kid. ']'
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


    /**
     * @Route("/{id_kid}/books/read", name="show_books_read", methods="GET", requirements={"id"="\d+"})
     * @return Response
     */

    public function showBookRead(int $id_kid, kidRepository $kidRepository, BookKidRepository $bookKidRepository){

        $currentKid = $kidRepository->find($id_kid);

        if ($currentKid === null )
            {

                $error = [
                    'error' => true,
                    'message' => 'No kid found for Id [' . $id_kid . ']'
                ];
                return $this->json($error, Response::HTTP_NOT_FOUND);
            }

            $currentReadbooks = $bookKidRepository->findAllByIsRead(true, $id_kid);

            return $this->prepareResponse(
                'OK',
                ['groups' => 'books_read'],
                ['data' => $currentReadbooks ]
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

    /*******************************************************************************************/

       /**
     * @Route("/{id_kid}/books/wish", name="show_book_wish_list", methods="GET", requirements={"id"="\d+"})
     * @return Response
     */

    public function WishToRead(int $id_kid, kidRepository $kidRepository, BookKidRepository $bookKidRepository){

        $currentKid = $kidRepository->find($id_kid);

        if ($currentKid === null )
            {

                $error = [
                    'error' => true,
                    'message' => 'No kid found for Id [' . $id_kid . ']'
                ];
                return $this->json($error, Response::HTTP_NOT_FOUND);
            }

            $currentBooksWish = $bookKidRepository->findAllByIsRead(false, $id_kid);

            return $this->prepareResponse(
                'OK',
                ['groups' => 'books_wish'],
                ['data' => $currentBooksWish ]
            );
        }

    /*******************************************************************************************/
}
