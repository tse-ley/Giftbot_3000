<?php
namespace App\Controller\BackController\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('order')]
#[IsGranted('ROLE_ADMIN')]
class OrderController extends AbstractController
{
    #[Route('', name: 'admin_order_index')]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('admin/order/index.html.twig', [
            'orders' => $orderRepository->findAllWithDetails()
        ]);
    }

    #[Route('/{id}', name: 'admin_order_show')]
    public function show(Order $order): Response
    {
        return $this->render('admin/order/show.html.twig', [
            'order' => $order
        ]);
    }

    #[Route('/{id}/update-status', name: 'admin_order_update_status')]
    public function updateStatus(
        Request $request,
        Order $order,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isCsrfTokenValid('update-status'.$order->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token');
            return $this->redirectToRoute('admin_order_show', ['id' => $order->getId()]);
        }
        
        $newStatus = $request->request->get('status');
        
        if (in_array($newStatus, Order::getStatuses())) {
            $order->setStatus($newStatus);
            $em->flush();
            $this->addFlash('success', 'Order status updated');
        }

        return $this->redirectToRoute('admin_order_show', ['id' => $order->getId()]);
    }
}