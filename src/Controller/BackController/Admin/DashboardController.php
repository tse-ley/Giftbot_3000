<?php
namespace App\Controller\BackController\Admin;

use App\Repository\GiftRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    #[Route('/', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        // Fetch repositories using the container
        $userRepository = $this->container->get(UserRepository::class);
        $giftRepository = $this->container->get(GiftRepository::class);
        $orderRepository = $this->container->get(OrderRepository::class);

        return $this->render('admin/dashboard/index.html.twig', [
            'userCount' => $userRepository->count([]),
            'giftCount' => $giftRepository->count([]),
            'orderCount' => $orderRepository->count([]),
            'recentOrders' => $orderRepository->findRecent(5),
        ]);
    }
}