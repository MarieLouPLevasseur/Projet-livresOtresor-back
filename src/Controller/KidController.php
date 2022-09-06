<?php

namespace App\Controller;

use App\Entity\Kid;
use App\Entity\BookKid;
use Doctrine\ORM\EntityManager;
use App\Repository\KidRepository;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use App\Repository\AvatarRepository;
use App\Repository\BookKidRepository;
use App\Repository\DiplomaRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * Update a kid avatar only
     * 
     * @Route("/{id}/avatar", name="update_kid_avatar", methods="PATCH", requirements={"id"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */

    public function updateAvatar(
        int $id,
        EntityManagerInterface $em, 
        KidRepository $kidRepository,
        Request $request, 
        SerializerInterface $serializer,
        ValidatorInterface $validator
        )
    {

        $kid = $kidRepository->find($id);

        // CHECK KID exists
            if ($kid === null )
            {
                return $this->ErrorMessageNotFound("The kid not found for id: ", $id);
            }

        $data = $request->getContent();
        $dataKid = $serializer->deserialize($data, Kid::class, 'json');

        // CHECK datas given

        if($dataKid->getProfileAvatar() == !null){
            $errors = $validator->validatePropertyValue($dataKid, 'profile_avatar', $dataKid->getProfileAvatar());
            if ((count($errors) > 0) ){
               
                return $this->ErrorMessageNotValid($errors);

            }   
            $kid->setProfileAvatar($dataKid->getProfileAvatar());
        } 

        $em->persist($kid);
        $em->flush();

        return $this->prepareResponse('Sucessfully updated', [], [], false, Response::HTTP_OK );
    }

     /**
     * Update a Book
     *
     * @Route("/{id_kid}/bookkids/{id_bookKid}", name="update_book", methods="PATCH", requirements={"id_kid"="\d+"}, requirements={"id_bookKid"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function updateBook( 
        int $id_kid,
        int $id_bookKid,
        BookKidRepository $bookKidRepository,
        KidRepository $kidRepository,
        SerializerInterface $serializer,
        Request $request,
        ValidatorInterface $validator,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em
        ): Response
        {

            $data = $request->getContent();
            $dataKid = $serializer->deserialize($data, BookKid::class, 'json');
            
            // CHECK KID

                $kid = $kidRepository->find($id_kid);

                    if ($kid === null )
                    {
                        return $this->ErrorMessageNotFound("The kid not found for id: ", $id_kid);

                    }

            // CHECK BOOK_KID

                $currentBookKid = $bookKidRepository->find($id_bookKid);

                    if ($currentBookKid === null )
                    {
                        return $this->ErrorMessageNotFound("No Bookid found for id: ", $id_bookKid);

                    }   


            // CHECK CATEGORY
            
                $parsed_json = json_decode($data);

                // Check if category exists in given json

                    if ($dataKid->getCategory() !== null) {
                        
                        $categoryID = $parsed_json->{"category"}->{"id"};
                        $categoryGiven= $categoryRepository->find($categoryID);
                        
                        
                        // if exist in json: check if exists in database
                        if ($categoryGiven === null) {

                            return $this->ErrorMessageNotFound("The Category not found for id: ", $categoryID);

                        }

                        // Check datas given

                        $errors = $validator->validatePropertyValue($dataKid, 'category', $dataKid->getCategory());
                        
                        if ((count($errors) > 0) ){
                            
                            return $this->ErrorMessageNotValid($errors);

                        }   
    
                        $currentBookKid->setCategory($categoryGiven);


                    }
                 
            // CHECK RATING if given

                if($dataKid->getRating() !== null){
                    
                    $errors = $validator->validatePropertyValue($dataKid, 'rating', $dataKid->getRating());
                    if ((count($errors) > 0) ){
                        
                        return $this->ErrorMessageNotValid($errors);

                    }  
                    
                    $currentBookKid->setRating($dataKid->getRating());
                
                } 
            // CHECK COMMENT if given

                if($dataKid->getComment() !== null){
                    
                    $errors = $validator->validatePropertyValue($dataKid, 'comment', $dataKid->getComment());
                    if ((count($errors) > 0) ){
                        
                        return $this->ErrorMessageNotValid($errors);

                    }   
                    $currentBookKid->setComment($dataKid->getComment());
                } 
                
            // CHECK IS_READ if given

                if($dataKid->getIsRead() !== null){
                    
                    $errors = $validator->validatePropertyValue($dataKid, 'is_read', $dataKid->getIsRead());
                    if ((count($errors) > 0) ){
                        
                        return $this->ErrorMessageNotValid($errors);

                    }   
                    $currentBookKid->setIsRead($dataKid->getIsRead());
                }   
                

            $em->persist($currentBookKid);
            $em->flush();

            return $this->prepareResponse('Sucessfully updated', [], [], false, Response::HTTP_OK );
        }

        
        // @IsGranted("IS_AUTHENTICATED_FULLY")

    /**
    * Show last book modified for a kid
    *
    * @Route("/{id_kid}/books/last_read", name="last_book_read", methods="GET", requirements={"id_kid"="\d+"})
    * @IsGranted("IS_AUTHENTICATED_FULLY")
    * 
    */
    public function showLastReadBook( 
        int $id_kid,
        BookKidRepository $bookKidRepository,
        KidRepository $kidRepository,
        SerializerInterface $serializer
        ): Response
    {

        $currentkid = $kidRepository->find($id_kid);

        // CHECK KID

            if ($currentkid === null )
            {
                return $this->ErrorMessageNotFound("The kid not found for id: ", $id_kid);

            }

        $mostRecentBook= $bookKidRepository->findBy(["kid"=>$id_kid], ['updated_at' => 'DESC'],1);

            // dd($mostRecentBook);
            $jsonMostRecentBook ="";
            foreach ($mostRecentBook as $currentBook){

                $jsonMostRecentBook = $serializer->serialize($currentBook, 'json',['groups' => 'last_book_read']);
            }
            // $jsonMostRecentBook = $serializer->serialize($mostRecentBook, 'json',['groups' => 'last_book_read']);
            return new JsonResponse($jsonMostRecentBook, 200,[],true );


        // return $this->prepareResponse(
        //     'OK',
        //     ['groups' => 'last_book_read'],
        //     ['Bookkid' => $mostRecentBook ]
        // );

    }

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

                return $this->ErrorMessageNotFound("The kid not found for id: ", $id_kid);

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

        //******** CHECK CURRENT LEVEL: ***********
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

            // ! SET FALSE DATA TO GIVE to front to set a false progress bar really high 
            // $finalMinimumGap=$minimumGapValue[0];

            // $lastTargetKeyArray= array_keys($gapArray,$finalMinimumGap);
            // $lastGoalReached = $lastTargetKeyArray[0];

            // $newGoal = 3000; //! set to really high : cannot be hit by kid

            // $lastlevelKeyArray = array_keys($rewardsArray, $lastGoalReached);
            // $currentLevel = $lastlevelKeyArray[0];

            // $newLevel= $currentLevel+1;


            // $nbBookToWinLevel= ($newGoal-$totalReadBooks);

            // ! OR SEND ERROR to write a message if hit 

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
            
            $completion = floor((($finalMinimumGap/($finalMinimumGap+$nbBookToWinLevel))*100));
        

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
     * List all avatars of a kid
     * 
     * @Route("/{id_kid}/avatars", name="show_avatars", methods="GET", requirements={"id_kid"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function listAllAvatars(
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

                return $this->ErrorMessageNotFound("The kid not found for id: ", $id_kid);

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
     * List all diplomas of a kid
     * 
     * @Route("/{id_kid}/diplomas", name="show_diplomas", methods="GET", requirements={"id_kid"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function listAllDiplomas(
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

            return $this->ErrorMessageNotFound("The kid not found for id: ", $id_kid);

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
                
                return $this->ErrorMessageNotFound("The kid is not found for id: ", $id_kid);

            }

            if ($currentBook === null )
            {
                
                return $this->ErrorMessageNotFound("The book is not found for id: ", $id_book);

            }

            if ($currentBookKid === [] )
            {
                
                return $this->ErrorMessageNotFound("The Bookkid is not found for id: ", $currentBookKid);

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
            BookKidRepository $bookKidRepository,
            CategoryRepository $categoryRepository
    )  

    {
        // ********  DATAS ************


        $data = $request->getcontent();
        $bookKid = $serializer->deserialize($data, BookKid::class, 'json');
        $kid = $kidRepository->find($id_kid);


        // ********  CHECK ERRORS ************

        $errors = $validator->validate($bookKid);

        if ((count($errors) > 0) ){

            return $this->ErrorMessageNotValid($errors);

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

        // ********  SET Category ************

            $category = $categoryRepository->findOneBy(['name'=>"Non-classé"],);
            // dd($category);
            $bookKid->setCategory($category);

        // persist
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
     * esponse
     */
    public function listAllBookOfOneKid( int $id_kid, KidRepository $kidRepository, BookRepository $bookRepository): Response
    {
        $kid = $kidRepository->find($id_kid);

        // CHECK KID exists

        if ($kid === null )
        {

            return $this->ErrorMessageNotFound("The kid is not found for id: ", $id_kid);

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

        // CHECK KID exists

        if ($currentKid === null )
            {

                return $this->ErrorMessageNotFound("The kid is not found for id: ", $id_kid);

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

        // CHECK KID exists

        if ($currentKid === null )
            {

                return $this->ErrorMessageNotFound("The kid is not found for id: ", $id_kid);

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
    public function listAuthors(int $id_kid, kidRepository $kidRepository, AuthorRepository $authors,BookKidRepository $bookKidRepository, SerializerInterface $serializer): Response
    {
        $kid = $kidRepository->find($id_kid);
        // $kid = $kidRepository->find($id_kid);

        // CHECK KID exists

        if ($kid === null )
        {
            return $this->ErrorMessageNotFound("The kid is not found for id: ", $id_kid);

        }
        $allBookKid = $kid->getBookKids();
      

        $allBooks = [];
        foreach ($allBookKid as $bookKid){
            
            $book = $bookKid->getBook();
            $allBooks [] = $book;
        }

        $allAuthors = $bookKidRepository->findByAuthors($id_kid);
        // dd($test);
        // $allAuthors = [];
        // foreach($allBooks as $currentBook){
        //     $author = $currentBook->getAuthors();
        //     // dd($author);
        //     // $name = $author->getName();
        //     // if (!in_array($name,$allAuthors)) {
        //         $allAuthors []= $author;
        //     // }
        // }

        // ***********************test**************
        // $booksAuthors = $authors->getBook();

        // $currentBook=[];
        //     foreach ($booksAuthors as $book){

        //         $bookkid = $bookKidRepository->findOneByKidandBook($id_kid, $book->getId());

        //         if($bookkid !== null){
        //         $currentBook [] = $book;
        //         }
        //     }       
        // dd($currentBook);
        // ***************************************
        // dd($allAuthors);

        // $allName =[];
        // foreach($allAuthors as $currentAuthor){

        //     $name = $currentAuthor->name;
        //     $allName []= $name;
        // }
        // $test = $bookKidRepository->findOneByKidandBook($id_kid,);

        // récupere tous les book d'un enfant avec findOneByKidandBook

        // recupere tous les book des auteurs complet

        // compare les deux tableaux et ejecte ceux non présent

        // dd($allAuthors);

        // $authors = $bookKid->getBook()->getAuthors();
        // foreach ($authors as $author) {
        //     $nameAuthorGiven = $author->getName();
            
        //     $isAuthorInBase = $authorRepository->findAuthorByName($nameAuthorGiven);
            
        //     if ($isAuthorInBase !== []) {
        //         // if exist set this one and don't let create a new author with same name

        //         foreach ($isAuthorInBase as $authorToSetFromBase) {
        //             $bookKid->getBook()->removeAuthor($author);
        //             $bookKid->getBook()->addAuthor($authorToSetFromBase);
        //         }
        //     }      
        // }
        
            $jsonBookKid = $serializer->serialize($allAuthors, 'json',['groups' => 'author_list'] );
            // dd($jsonBookKid);
        // return $this->json($jsonBookKid,200);
        // return $this->prepareResponse(
        //         'OK',  
        //         ['groups' => 'author_list'],
        //         ['authors' => $allAuthors]
        //     );

        $data = [
            "authors"=>$allAuthors
        ];
        
        return new JsonResponse($data, Response::HTTP_OK, [],false);
    }


     /**
     * List all books of an author for a kid
     * 
     * @Route("/{id_kid}/books/authors/{author_id}", name="list_books_of_one_author", methods="GET", requirements={"id_kid"="\d+", "author_id"="\d+"})
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

         // CHECK KID exists

        if ($kid === null )
        {

            return $this->ErrorMessageNotFound("The kid is not found for id: ", $id_kid);

        }

        $authors = $authorsRepository->find($author_id);

         // CHECK AUTHOR exists

        if ($authors === null )
        {

            return $this->ErrorMessageNotFound("The Author is not found for id: ", $author_id);

        }

        $booksAuthors = $authors->getBook();

        $bookkidArray=[];
            foreach ($booksAuthors as $book){

                $bookkid = $bookKidRepository->findOneByKidandBook($id_kid, $book->getId());

                $bookkidArray [] = $bookkid;
            }       
        
        return $this->prepareResponse(
            'OK',  
            ['groups' => 'books_infos'],
            ['data' => $bookkidArray]
        ); 
    }

     /**
     * @Route("/{id_kid}/bookkids/{id_bookKid}", name="delete_book", methods="DELETE", requirements={"id_kid"="\d+", "id_bookKid"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */
   
     public function deleteBooks(int $id_kid, int $id_bookKid ,BookKidRepository $bookKidRepository, EntityManagerInterface $em, KidRepository $kidRepository){

        $kid = $kidRepository->find($id_kid);
        $bookKid = $bookKidRepository->find($id_bookKid);
   
        // CHECK KID exists

        if ($kid === null )
        {

            return $this->ErrorMessageNotFound("The kid is not found for id: ", $id_kid);

        }

        // CHECK BOOKKID exists

        if ($bookKid === null )
        {

            return $this->ErrorMessageNotFound("The Bookkid is not found for id: ", $id_bookKid);

        }

        $currentBookKid = $kid->getBookKids();
        
        $arrayBookkid = [];

            foreach($currentBookKid as $bookkid){
                $arrayBookkid [] = $bookkid;
            }

            if (!in_array($bookKid, $arrayBookkid)){

                $error = [
                    'error'=> true,
                    'message'=> "The book is not own by this kid. Can't delete this book."
                ];

                return $this->json($error, Response::HTTP_NOT_FOUND);
            }

        $em->remove($bookKid);
        $em->flush();

        return $this->prepareResponse("the book has been remove successfully",[],[],false,200);

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
     * Send error message if not found
     * @param string $message error message to send if not found
     * @param int $id id
     */
    private function ErrorMessageNotFound( $messageError, $id){

        
        $error = [
            'error' => true,
            'message' => $messageError."[" . $id . "]"
        ];
        return $this->json($error, Response::HTTP_NOT_FOUND);         

    }
     /**
     * Sent error message if not valid
     * @param mixed $errors errors found during validation
     * 
     */
    private function ErrorMessageNotValid($errors){

        if ((count($errors) > 0)) {

             /*
            * Uses a __toString method on the $errors variable which is a
            * ConstraintViolationList object. This gives us a nice string
            * for debugging.
            */
 
            $errorsString = (string) $errors;
            $error = [
                'error' => true,
                'message' => $errorsString
            ];

            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }
    }
}
