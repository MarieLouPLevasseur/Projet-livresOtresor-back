<?php

namespace App\Controller;

use App\Entity\BookKid;
use App\Repository\AuthorRepository;
use App\Repository\KidRepository;
use App\Repository\BookRepository;
use App\Repository\AvatarRepository;
use App\Repository\BookKidRepository;
use App\Repository\DiplomaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Serializer;

/**
 * Kid class
 * @Route("/api/v1/kids", name="api_kids_")
 */
class KidController extends AbstractController
{
    /**
     * Show element for progress bar
     *
     * @Route("/{id_kid}/books/progress_bar", name="progress_bar", methods="GET")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function progressBar( 
        int $id_kid,
        BookKidRepository $bookKidRepository,
        AvatarRepository $avatarRepository,
        KidRepository $kidRepository
        ): Response
    {

        // *** CHECK if KID EXISTS ******

        $currentKid = $kidRepository->find($id_kid);

            if ($currentKid === null )
            {

                $error = [
                    'error' => true,
                    'message' => 'No Kid found for Id [' . $id_kid . ']'
                ];

                return $this->json($error, Response::HTTP_NOT_FOUND); 
            }

        // *** GET REWARDING LEVELS : from Avatars "is_win" Value      

        $avatarsObjects= $avatarRepository->findBy([],['is_win' => 'ASC']);

        // RewardsArray:
            // key = level (A)
            // value = number of read book to start the level (B)

        $rewardsArrayRaw = [];

            foreach($avatarsObjects as $avatar){

                $isWinValue = $avatar->getIsWin();

                $rewardsArrayRaw []= $isWinValue;

            }
       
        // SET UNIQUE LEVELS (same value is_win may exists in database=> take them away)

        $rewardsArrayUnique = array_unique($rewardsArrayRaw);
        $rewardsArray = array_values($rewardsArrayUnique);

        // *** GET READ BOOKS : "is_read" value "true" in BookKid
        
        $ReadBooks = $bookKidRepository->findAllByIsRead(true, $id_kid);
        $totalReadBooks = count($ReadBooks);
        // $totalReadBooks = 46;

        //******* */ CHECK CURRENT LEVEL: ***********
        // SET intermediate array where:
            // key = number of read books to start the level (B)
            // value = difference from total read books and amount of book needed to be read for the level (C)
            // goal: 
                // find all differences with goal but get rid off value higher than total read book (higher level)
                // be able to go back up to arrayAwards and regain level (A) by setting (B) as key
            
        $gapArray=[]; 

        foreach($rewardsArray as $reward)
        {

            $difference= ($totalReadBooks-$reward);

            if ($difference == 0 || $difference>0) {
                $gapArray[$reward]= $difference;
            }
            
        }

        // SET Final Table with minimum Gap value to find the good level
            // key: will always be 0 since value in array will always be replace if a lower value is found
            // value: the lower gap found (the minimum gap from total read and amount needed for level), last (C) value
            // goal: get the minimum gap et found the current level

        $minimumGapValue= []; 

            foreach ($gapArray as $difference) {

                $count=count($minimumGapValue);

                if ($count==0) {
                    $minimumGapValue[]= $difference;
                }
                //if last element is lower value, replace et set in array
                if ($minimumGapValue[0]>=$difference) {
                    $minimumGapValue= [];
                    $minimumGapValue[]= $difference;
                }
            }

       
        // 0 BOOK READ: set manually since there is no lower level to get back to
        if ($totalReadBooks == 0) {

            $lastGoalReached = 0;
            $newGoal = 1;
            $currentLevel = 0;
            $finalMinimumGap = 0;
            $gapArray[0] = 0;
            $newLevel= 1;

        }
        // BOOKS READ HIGHER THAN LAST REWARD : set manually since there is no higher level to compare to
        else if ($totalReadBooks >= end($rewardsArray)){

            // ! SET FALSE DATA TO GIVE to front to set a false progress bar really high ??
            // $finalMinimumGap=$minimumGapValue[0];

            // $lastTargetKeyArray= array_keys($gapArray,$finalMinimumGap);
            // $lastGoalReached = $lastTargetKeyArray[0];

            // $newGoal = 3000; //! set to really high : cannot be hit by kid

            // $lastlevelKeyArray = array_keys($rewardsArray, $lastGoalReached);
            // $currentLevel = $lastlevelKeyArray[0];

            // $newLevel= $currentLevel+1;


            // $nbBookToWinLevel= ($newGoal-$totalReadBooks);

            // ! OR SEND ERROR to write a message if hit ??

            $error = ["error"   => true,
                      "message" => "there is no more level actually available",
                      
            ];

            return $this->json($error, 409);
        }
        else{     
            
        // SET final value found: lower (C) value 
            // must be index [0] since a single value is expected
            $finalMinimumGap=$minimumGapValue[0];

        // GET intermediate Key (B): last goal Reach
            // back up to current amount book read to be at this level
            $lastTargetKeyArray= array_keys($gapArray,$finalMinimumGap);
            $lastGoalReached = $lastTargetKeyArray[0];

        // GET initial Key (A): current level

            $lastlevelKeyArray = array_keys($rewardsArray, $lastGoalReached);
            $currentLevel = $lastlevelKeyArray[0];

        // GET current level +1 : new level to reach
            $newLevel= $currentLevel+1;

        // Get new Goal : new amount of books to read
            $newGoal = $rewardsArray[$newLevel];
        }

            $nbBookToWinLevel= ($newGoal-$totalReadBooks);
            
            $isNewLevel = false;

                if ($finalMinimumGap === 0){

                    $isNewLevel = true;
                }
            
            $completion = (($finalMinimumGap/($finalMinimumGap+$nbBookToWinLevel))*100);
        

            $data = ["lastGoalReached"        => $lastGoalReached , 
                     "currentGoal"            => $newGoal,
                     "currentLevel"           => $currentLevel,
                     "newLevel"               => $newLevel,
                     "bookReadOnCurrentLevel" => $finalMinimumGap,
                     "bookToReadToNewLevel"   => $nbBookToWinLevel,
                     "isNewLevel"             => $isNewLevel,
                     "totalBooksReadByKids"   => $totalReadBooks,
                     "completion"             => $completion
            ];

        return $this->json($data, 200);

    }

     /**
     * List all books of a category for a kid
     * 
     * @Route("/{id_kid}/category/{id_cat}/books", name="show_category_books", methods="GET", requirements={"id_kid"="\d+"}, requirements={"id_cat"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function listBooksbyCategory(
        int $id_kid,
        int $id_cat,
        BookKidRepository $bookKidRepository,
        SerializerInterface $serializer
       )
    {

        $allBooksByCategory = $bookKidRepository->findAllByKidAndCategory($id_kid, $id_cat);

        $jsonBooksCategoryList = $serializer->serialize($allBooksByCategory, 'json',['groups' => 'booksByCategory']);

        return new JsonResponse($jsonBooksCategoryList, Response::HTTP_OK, [],true);
    }


    /**
     * Show all avatars of a kid
     * 
     * @Route("/{id_kid}/avatars", name="show_avatars", methods="GET", requirements={"id_kid"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
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
     * 
     * @Route("/{id_kid}/diplomas", name="show_diplomas", methods="GET", requirements={"id_kid"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
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
     * Show details for a book
     * 
     * @Route("/{id_kid}/books/{id_book}", name="show_book_details", methods="GET", requirements={"id_kid"="\d+"}, requirements={"id_book"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
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

        /*************************Routes coded using the prepare response method*******************************************************************/

     /**
     * Create a book
     * 
     * @Route("/{id_kid}/books", name="create_book", methods="POST", requirements={"id_kid"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */
    public function createBookKid(
            int $id_kid,
            Request $request,
            SerializerInterface $serializer,
            ValidatorInterface $validator,
            EntityManagerInterface $em,
            AuthorRepository $authorRepository,
            BookRepository $bookRepository,
            KidRepository $kidRepository,
            BookKidRepository $bookKidRepository
    )  

    {
        // ********  DATAS ************


        $data = $request->getcontent();
        $bookKid = $serializer->deserialize($data, BookKid::class, 'json');
        $kid = $kidRepository->find($id_kid);


        // ********  CHECK ERRORS ************

        $errorsBookKid = $validator->validate($bookKid);

        if ((count($errorsBookKid) > 0) ){
            /*
            * Uses a __toString method on the $errors variable which is a
            * ConstraintViolationList object. This gives us a nice string
            * for debugging.
            */
            $errorsStringBook = (string) $errorsBookKid;

            $error = [
                'error' => true,
                'message book' => $errorsStringBook
            ];
            return $this->json($error, Response::HTTP_BAD_REQUEST);

        }

        // ********  CHECK if authors exists ************

            $authors = $bookKid->getBook()->getAuthors();
            foreach ($authors as $author) {
                $nameAuthorGiven = $author->getName();
                
                $isAuthorInBase = $authorRepository->findAuthorByName($nameAuthorGiven);
                
                if ($isAuthorInBase !== []) {
                    // if exist set this one and don't let create a new author with same name

                    foreach ($isAuthorInBase as $authorToSetFromBase) {
                        $bookKid->getBook()->removeAuthor($author);
                        $bookKid->getBook()->addAuthor($authorToSetFromBase);
                    }
                }      
            }
                

        // ********  CHECK if ISBN exists ************

            $isbnGiven = $bookKid->getBook()->getIsbn();

            $isbnExistingInBook = $bookRepository->findOneByIsbnCode($isbnGiven);

            if ($isbnExistingInBook !== null){
            // if exist: set book from database


                $bookKid-> setBook($isbnExistingInBook);

                // If the book already exists then the Book kid might exists too

                            // ********  CHECK if BOOK KID exists ************

                $bookKidExist = $bookKidRepository->findOneByKidandBook($id_kid,$isbnExistingInBook->getId());

                    if($bookKidExist !== []){

                        $error = [
                            'error' => true,
                            'message' => 'The book [' .$isbnExistingInBook->getId() . '] already exist for the kid [' . $id_kid . ']'
                        ];
                        return $this->json($error, Response::HTTP_CONFLICT);
        
        
                    }

            }
                // ********  CHECK if Cover exists ************

                $coverExist = $bookKid->getBook()->getCover();

                if ($coverExist === null){

                    $bookKid->getBook()->setCover("https://i.pinimg.com/564x/11/1b/59/111b5913903c2bfbe7f11487bb3f06f6.jpg");
                }


        // ********  SET Kid ************

            $bookKid->setKid($kid);

            $em->persist($bookKid);

            $em->flush();

            return $this->prepareResponse(
                'The book has been created',[],[],false, 201, 
            );
    }

     /**
     * List all books for a kid
     * 
     * @Route("/{id_kid}/books", name="show_book_list", methods="GET", requirements={"id"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */
    public function listAllBookOfOneKid( int $id_kid, KidRepository $kidRepository, BookRepository $bookRepository): Response
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

        return $this->prepareResponse(
            'OK',
            ['groups' => 'books_infos'],
            ['data' => $bookKid]
        );
    }


    /**
     * List all books read for a kid
     * 
     * @Route("/{id_kid}/books/read", name="show_books_read", methods="GET", requirements={"id"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */
    public function listBookRead(int $id_kid, kidRepository $kidRepository, BookKidRepository $bookKidRepository)
    {

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


    /**
     * List all books wished for a kid
     * 
     * @Route("/{id_kid}/books/wish", name="show_book_wish_list", methods="GET", requirements={"id"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */
    public function listBookWishToRead(int $id_kid, kidRepository $kidRepository, BookKidRepository $bookKidRepository)
    {

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

     /**
     * List all authors names
     * 
     * @Route("/{id_kid}/books/authors", name="show_author_list", methods="GET", requirements={"id"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */
    public function listAuthors(int $id_kid, kidRepository $kidRepository, AuthorRepository $authors, SerializerInterface $serializer): Response
    {
        $kid = $kidRepository->find($id_kid);
        $allBookKid = $kid->getBookKids();

        if ($kid === null )
        {
            $error = [
                'error' => true,
                'message' => 'No kid found for Id [' . $id_kid . ']'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
        }
 
        $allBooks = [];
        foreach ($allBookKid as $bookKid){

            $book = $bookKid->getBook();
            $allBooks [] = $book;
        }

        $allAuthors = [];
        foreach($allBooks as $currentBook){
            $author = $currentBook->getAuthors();
            $allAuthors []= $author;
        }

        $jsonBookKid = $serializer->serialize($allAuthors, 'json',['groups' => 'author_list'] );
        return new JsonResponse($jsonBookKid, Response::HTTP_OK, [],true);
    }


     /**
     * List all books of an author for a kid
     * 
     * @Route("/{id_kid}/books/authors/{author_id}", name="show_books_of_one_author", methods="GET", requirements={"id_kid"="\d+", "author_id"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */
    public function listAllBooksbyAuthor(
        int $id_kid,
        int $author_id,
        kidRepository $kidRepository,
        AuthorRepository $authorsRepository,
        BookKidRepository $bookKidRepository,
        BookRepository $bookRepository,
        SerializerInterface $serializer
        ): Response
    {
        $kid = $kidRepository->find($id_kid);
        $authors = $authorsRepository->find($author_id);

        $book_id = $bookRepository->findAll();   

        $booksAuthors = $authors->getBook();

        $bookkidArray=[];
        foreach ($booksAuthors as $book){

            $bookkid = $bookKidRepository->findOneByKidandBook($id_kid, $book->getId());

            $bookkidArray [] = $bookkid;
        }

        if ($kid === null )
        {

            $error = [
                'error' => true,
                'message' => 'No books found for Id [' . $book_id. ']'
            ];
            return $this->json($error, Response::HTTP_NOT_FOUND);
        }
        
        return $this->prepareResponse(
            'OK',  
            ['groups' => 'books_infos'],
            ['data' => $bookkidArray]
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
}
