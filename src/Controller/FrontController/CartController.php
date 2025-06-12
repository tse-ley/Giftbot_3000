<?php
namespace App\Controller\FrontController;

use App\Entity\CartItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\CartItemRepository;
use App\Repository\GiftRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mime\Address;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('', name: 'app_cart')]
    public function index(Request $request, CartItemRepository $cartItemRepository, MailerInterface $mailer, EntityManagerInterface $em, GiftRepository $giftRepository): Response
    {
        if ($request->isMethod('POST')) {
            // Handle localStorage cart data for checkout
            $cartData = json_decode($request->request->get('cart_data', '[]'), true);
            
            if (empty($cartData)) {
                $this->addFlash('error', 'Your cart is empty.');
                return $this->redirectToRoute('app_cart');
            }

            $total = 0;
            $orderItems = [];

                // Process each item from localStorage
                foreach ($cartData as $item) {
                    if (empty($item['id'])) {
                        continue;
                    }
                    $gift = $giftRepository->find($item['id']);
                    if ($gift) {
                        $quantity = $item['quantity'] ?? 1;
                        $total += $gift->getPrice() * $quantity;
                        $orderItems[] = [
                            'gift' => $gift,
                        'quantity' => $quantity
                    ];
                }
            }

            // Send confirmation email
            try {
                $email = (new Email())
                    ->from(new Address('noreply@example.com', 'Giftbot 3000'))
                    ->to($this->getUser()->getEmail())
                    ->subject('Order Confirmation')
                    ->html($this->renderView('emails/order_confirmation.html.twig', [
                        'orderItems' => $orderItems,
                        'total' => $total,
                        'user' => $this->getUser()
                    ]));

                $mailer->send($email);
                $this->addFlash('success', 'Order placed successfully! Check your email for confirmation.');
                
                // Clear the cart (will be handled by JavaScript)
                return $this->redirectToRoute('app_cart', ['order_success' => true]);
                
            } catch (\Exception $e) {
                $this->addFlash('error', 'There was an error processing your order. Please try again.');
                return $this->redirectToRoute('app_cart');
            }
        }

        // For logged-in users, you might want to show DB cart as well
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
        $giftId = $request->request->get('gift_id'); // Fixed: get gift_id from request
        
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
