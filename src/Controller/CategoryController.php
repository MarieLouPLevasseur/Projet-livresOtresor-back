<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends AbstractController
{
    /**
     * @Route("/categories", name="category")
     */
    public function categoryName(CategoryRepository $categoryRepository, SerializerInterface $serializer): Response
    {
        $categoryList = $categoryRepository->findall();
        $jsonCategoryList = $serializer->serialize($categoryList, 'json');

        return new JsonResponse($jsonCategoryList, Response::HTTP_OK, [], true);
    }
}
