<?php

namespace App\Controller\FrontController;

use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\CartItemRepository;
use App\Repository\GiftRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
class CartController extends AbstractController
{
    /**
     * Displays the cart page.
     */
    #[Route('', name: 'app_cart', methods: ['GET'])]
    public function index(Request $request, CartItemRepository $cartItemRepository): Response
    {
        // For logged-in users, show the cart items saved in the database.
        $cartItems = $this->getUser()
            ? $cartItemRepository->findBy(['user' => $this->getUser()])
            : [];

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            // This parameter is passed by the placeOrder action upon successful redirection.
            'orderSuccess' => $request->query->get('order_success', false),
        ]);
    }

    /**
     * Handles the creation of an order from the cart data submitted via POST.
     */
    #[Route('/place-order', name: 'app_cart_place_order', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function placeOrder(
        Request $request,
        CartItemRepository $cartItemRepository,
        GiftRepository $giftRepository,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        LoggerInterface $logger // Injected Logger for better error logging
    ): Response {
        // We know the user exists because of the IsGranted attribute
        $user = $this->getUser();

        $cartData = json_decode($request->request->get('cart_data', '[]'), true);

        if (empty($cartData)) {
            $this->addFlash('error', 'Your cart is empty.');
            return $this->redirectToRoute('app_cart');
        }

        $total = 0;
        $orderItemsData = [];

        // Validate and prepare order items
        foreach ($cartData as $item) {
            if (empty($item['id'])) {
                continue;
            }
            $gift = $giftRepository->find($item['id']);
            if ($gift) {
                $quantity = $item['quantity'] ?? 1;
                $price = $gift->getPrice();
                $total += $price * $quantity;
                $orderItemsData[] = [
                    'gift' => $gift,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            }
        }

        if (empty($orderItemsData)) {
            $this->addFlash('error', 'No valid items found in your cart.');
            return $this->redirectToRoute('app_cart');
        }

        // Persist the Order and OrderItems
        $order = new Order();
        $order->setUser($user);
        $order->setTotal($total);
        $order->setCreatedAt(new \DateTime());

        foreach ($orderItemsData as $itemData) {
            $orderItem = new OrderItem();
            $orderItem->setGift($itemData['gift']);
            $orderItem->setQuantity($itemData['quantity']);
            $orderItem->setPrice($itemData['price']);
            $orderItem->setOrder($order);
            $em->persist($orderItem);
            $order->addOrderItem($orderItem);
        }

        $em->persist($order);

        // Clear the user's cart in the DB after successful order creation
        $cartItems = $cartItemRepository->findBy(['user' => $user]);
        foreach ($cartItems as $cartItem) {
            $em->remove($cartItem);
        }

        $em->flush();

        // Send confirmation email
        $email = (new Email())
            ->from(new Address('noreply@example.com', 'Giftbot 3000'))
            ->to($user->getEmail())
            ->subject('Order Confirmation')
            ->html($this->renderView('emails/order_confirmation.html.twig', [
                'orderItems' => $orderItemsData,
                'total' => $total,
                'user' => $user,
                'order' => $order,
            ]));

        try {
            $mailer->send($email);
            $this->addFlash('success', 'Order placed successfully! Check your email for confirmation.');
        } catch (\Exception $e) {
            $this->addFlash('warning', 'Order placed, but the confirmation email could not be sent. Please contact support.');
            // Using the injected logger is better than error_log()
            $logger->error('Failed to send order confirmation email: ' . $e->getMessage());
        }

        // Redirect to cart with success flag to clear localStorage via JavaScript
        return $this->redirectToRoute('app_cart', ['order_success' => 1]);
    }


    #[Route('/add', name: 'app_cart_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function add(
        Request $request,
        GiftRepository $giftRepository,
        CartItemRepository $cartItemRepository,
        EntityManagerInterface $em
    ): Response {
        // This action's logic is fine as is
        $giftId = $request->request->get('gift_id');
        $gift = $giftRepository->find($giftId) ?? throw $this->createNotFoundException('Gift not found');

        $cartItem = $cartItemRepository->findOneBy(['user' => $this->getUser(), 'gift' => $gift]);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setUser($this->getUser());
            $cartItem->setGift($gift);
            $cartItem->setQuantity(1);
            $em->persist($cartItem);
        }

        $em->flush();
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function remove(CartItem $cartItem, EntityManagerInterface $em): Response
    {
        // This action's logic is fine as is
        $this->denyAccessUnlessGranted('DELETE', $cartItem);
        $em->remove($cartItem);
        $em->flush();
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/checkout', name: 'app_cart_checkout')]
    #[IsGranted('ROLE_USER')]
    public function checkout(): Response
    {
        // This action likely just renders a page, so it's fine as is.
        return $this->render('cart/checkout.html.twig');
    }
}