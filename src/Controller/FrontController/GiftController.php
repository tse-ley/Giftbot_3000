<?php
namespace App\Controller\FrontController;

use App\Entity\Gift;
use App\Form\GiftSearchType;
use App\Repository\GiftRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GiftController extends AbstractController
{
    #[Route('/gifts', name: 'app_gifts')]
    public function index(Request $request, GiftRepository $giftRepository): Response
    {
        $form = $this->createForm(GiftSearchType::class);
        $form->handleRequest($request);

        $gifts = $form->isSubmitted() && $form->isValid() 
            ? $giftRepository->search($form->getData())
            : $giftRepository->findAll();

        return $this->render('gift/index.html.twig', [
            'gifts' => $gifts,
            'searchForm' => $form->createView()
        ]);
    }

    #[Route('/gifts/{id}', name: 'app_gift_show')]
    public function show(Gift $gift): Response
    {
        return $this->render('gift/show.html.twig', [
            'gift' => $gift
        ]);
    }
}