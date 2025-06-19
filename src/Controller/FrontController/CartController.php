<?php

namespace App\Controller\FrontController;

use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\CartItemRepository;
use App\Repository\GiftRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mime\Address;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('', name: 'app_cart')]
    public function index(
        Request $request,
        CartItemRepository $cartItemRepository,
        MailerInterface $mailer,
        EntityManagerInterface $em,
        GiftRepository $giftRepository
    ): Response {
        if ($request->isMethod('POST')) {
            // Handle localStorage cart data for checkout
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
            try {
                $order = new Order();
                $order->setUser($this->getUser());
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
                $em->flush();

                // Clear the user's cart in the DB after successful order creation
                if ($this->getUser()) {
                    $cartItems = $cartItemRepository->findByUser($this->getUser());
                    foreach ($cartItems as $cartItem) {
                        $em->remove($cartItem);
                    }
                    $em->flush();
                }

                // Send confirmation email
                $email = (new Email())
                    ->from(new Address('noreply@example.com', 'Giftbot 3000'))
                    ->to($this->getUser()->getEmail())
                    ->subject('Order Confirmation')
                    ->html($this->renderView('emails/order_confirmation.html.twig', [
                        'orderItems' => $orderItemsData,
                        'total' => $total,
                        'user' => $this->getUser(),
                        'order' => $order
                    ]));

                try {
                    $mailer->send($email);
                    $this->addFlash('success', 'Order placed successfully! Check your email for confirmation.');
                } catch (\Exception $e) {
                    $this->addFlash('warning', 'Order placed successfully, but there was an issue sending the confirmation email. Please contact support.');
                    // Log the error for further investigation
                    error_log('Email sending failed: ' . $e->getMessage());
                }

                // Redirect to cart with success flag
                return $this->redirectToRoute('app_cart', ['order_success' => true]);
            } catch (\Doctrine\ORM\ORMException $e) {
                $this->addFlash('error', 'There was an error processing your order. Please try again.');
                return $this->redirectToRoute('app_cart');
            }
        }

        // For logged-in users, show DB cart as well
        $cartItems = $this->getUser()
            ? $cartItemRepository->findByUser($this->getUser())
            : [];

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'orderSuccess' => $request->query->get('order_success', false)
        ]);
    }

    #[Route('/add', name: 'app_cart_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function add(
        Request $request,
        GiftRepository $giftRepository,
        CartItemRepository $cartItemRepository,
        EntityManagerInterface $em
    ): Response {
        $giftId = $request->request->get('gift_id');

        if (!$giftId) {
            throw $this->createNotFoundException('Gift ID not provided');
        }

        $gift = $giftRepository->find($giftId);
        if (!$gift) {
            throw $this->createNotFoundException('Gift not found');
        }

        $cartItem = $cartItemRepository->findOneBy([
            'user' => $this->getUser(),
            'gift' => $gift
        ]);

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

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    #[IsGranted('ROLE_USER')]
    public function remove(CartItem $cartItem, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $cartItem);
        $em->remove($cartItem);
        $em->flush();
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/checkout', name: 'app_cart_checkout')]
    #[IsGranted('ROLE_USER')]
    public function checkout(): Response
    {
        return $this->render('cart/checkout.html.twig');
    }

}
