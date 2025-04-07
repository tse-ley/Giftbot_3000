<?php

namespace App\Controller\BackController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminUserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_admin_user')]
    public function index(): Response
    {
        return $this->render('admin_user/index.html.twig', [
        ]);
    }
}
