<?php
namespace App\Controller\BackController;

use App\Repository\GiftRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function index(
        UserRepository $userRepository,
        GiftRepository $giftRepository,
        OrderRepository $orderRepository
    ): Response {
        return $this->render('admin/dashboard/index.html.twig', [
            'userCount' => $userRepository->count([]),
            'giftCount' => $giftRepository->count([]),
            'orderCount' => $orderRepository->count([]),
            'recentOrders' => $orderRepository->findRecent(5),
        ]);
    }
}