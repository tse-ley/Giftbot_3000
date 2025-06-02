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
        // Fetch repositories using dependency injection (preferred) or container
        $userRepository = $this->container->get(UserRepository::class);
        $giftRepository = $this->container->get(GiftRepository::class);
        $orderRepository = $this->container->get(OrderRepository::class);

        return $this->render('admin/dashboard/index.html.twig', [
            'userCount' => $userRepository->count([]),
            'giftCount' => $giftRepository->count([]),
            'orderCount' => $orderRepository->count([]),
            'recentOrders' => $orderRepository->findRecent(5),
            'user' => $this->getUser(), // Add current user for display
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin Dashboard')
            ->setFaviconPath('favicon.ico')
            ->setTranslationDomain('admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        
        yield MenuItem::section('Management');
        yield MenuItem::linkToCrud('Users', 'fas fa-users', \App\Entity\User::class);
        yield MenuItem::linkToCrud('Gifts', 'fas fa-gift', \App\Entity\Gift::class);
        yield MenuItem::linkToCrud('Orders', 'fas fa-shopping-cart', \App\Entity\Order::class);
        
        yield MenuItem::section('System');
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out-alt');
    }
}