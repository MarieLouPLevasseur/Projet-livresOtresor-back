<?php

namespace App\Controller;

use App\Repository\BookKidRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

/**
 * Kid class
 * @Route("/api/v1/kids", name="api_kids")
 */
class KidController extends AbstractController
{
    
    /**
     * @Route("", name="app_kid")
     */
    public function list()//: Response
    {

        // return new JsonResponse($jsonCategoryList, Response::HTTP_OK, [], true);

       
    }




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
}
