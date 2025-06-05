<?php
namespace App\Controller\BackController\Admin;

use App\Repository\GiftRepository;
use App\Repository\OrdersRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    private UserRepository $userRepository;
    private GiftRepository $giftRepository;
    private OrdersRepository $ordersRepository;

    public function __construct(
        UserRepository $userRepository,
        GiftRepository $giftRepository,
        OrdersRepository $ordersRepository
    ) {
        $this->userRepository = $userRepository;
        $this->giftRepository = $giftRepository;
        $this->ordersRepository = $ordersRepository;
    }

    #[Route('{slash}', name: 'admin_dashboard', requirements: ['slash' => '[/]?'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->render('admin/dashboard/index.html.twig', [
            'userCount' => $this->userRepository->count([]),
            'giftCount' => $this->giftRepository->count([]),
            'orderCount' => $this->ordersRepository->count([]),
            'recentOrders' => $this->ordersRepository->findRecent(5),
            'user' => $this->getUser(),
            'users' => $users,
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