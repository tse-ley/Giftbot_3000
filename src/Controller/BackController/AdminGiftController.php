<?php

namespace App\Controller\BackController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminGiftController extends AbstractController
{
    #[Route('/admin/gift', name: 'app_admin_gift')]
    public function index(): Response
    {
        return $this->render('admin_gift/index.html.twig', [
        ]);
    }
}
