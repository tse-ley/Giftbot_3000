<?php
namespace App\Controller;

use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\GiftRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    #[Route('', name: 'app_cart')]
    public function index(CartItemRepository $cartItemRepository): Response
    {
        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItemRepository->findByUser($this->getUser())
        ]);
    }

    #[Route('/add/{giftId}', name: 'app_cart_add')]
    public function add(
        int $giftId,
        GiftRepository $giftRepository,
        CartItemRepository $cartItemRepository,
        EntityManagerInterface $em
    ): Response {
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
    public function remove(CartItem $cartItem, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $cartItem);
        
        $em->remove($cartItem);
        $em->flush();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/checkout', name: 'app_cart_checkout')]
    public function checkout(): Response
    {
        // Implementation would go here
        return $this->render('cart/checkout.html.twig');
    }
}