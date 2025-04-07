<?php

namespace App\Controller\FrontController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GiftController extends AbstractController
{
    #[Route('/gift', name: 'app_gift')]
    public function index(): Response
    {
        return $this->render('gift/index.html.twig', [
        ]);
    }
}

