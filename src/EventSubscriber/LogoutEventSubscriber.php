<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutEventSubscriber implements EventSubscriberInterface
{
    private $cartItemRepository;
    private $entityManager;

    public function __construct(CartItemRepository $cartItemRepository, EntityManagerInterface $entityManager)
    {
        $this->cartItemRepository = $cartItemRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Defines the event this subscriber listens to.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }

    /**
     * This method is called when the LogoutEvent is dispatched.
     */
    public function onLogout(LogoutEvent $event): void
    {
        // Get the user from the security token BEFORE they are logged out.
        $user = $event->getToken()?->getUser();

        // Ensure we have a user of the correct type.
        if ($user instanceof User) {
            $cartItems = $this->cartItemRepository->findBy(['user' => $user]);

            foreach ($cartItems as $cartItem) {
                $this->entityManager->remove($cartItem);
            }

            $this->entityManager->flush();
        }
    }
}