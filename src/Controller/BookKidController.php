<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookKidController extends AbstractController
{
    /**
     * @Route("/book/kid", name="app_book_kid")
     */
    public function index(): Response
    {
        return $this->render('book_kid/index.html.twig', [
            'controller_name' => 'BookKidController',
        ]);
    }
}
