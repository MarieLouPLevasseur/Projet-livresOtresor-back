<?php

namespace App\Controller;

use App\Entity\BookKid;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Categories class
 * @Route("/api/v1/categories", name="api_category")
 */
class CategoryController extends AbstractController
{
    /**
     * List all category
     * @Route("", name="categorylist", methods="GET")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */
    public function categoryName(CategoryRepository $categoryRepository, SerializerInterface $serializer): Response
    {
        $categoryList = $categoryRepository->findall();
        $jsonCategoryList = $serializer->serialize($categoryList, 'json',['groups' => 'category']);

        return new JsonResponse($jsonCategoryList, Response::HTTP_OK, [], true);
    }



    
}
