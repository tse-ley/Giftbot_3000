<?php
namespace App\Controller\FrontController;

use App\Entity\User;
use App\Form\AccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\OrdersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/account')]
#[IsGranted('ROLE_USER')]
class UserAccountController extends AbstractController
{
    #[Route('', name: 'app_account')]
    public function index(OrdersRepository $ordersRepository): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        $orders = $ordersRepository->findBy(['user' => $user]);

        return $this->render('user_account/index.html.twig', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    #[Route('/edit', name: 'app_account_edit')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to edit your account.');
        }

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Account updated successfully');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
