<?php

namespace App\Controller\FrontController;

use App\Entity\Gift;
use App\Form\GiftSearchType;
use App\Repository\GiftRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GiftSearchController extends AbstractController
{
    #[Route('/giftsearch', name: 'app_giftsearch')]
    public function searchPage(Request $request, GiftRepository $giftRepository): Response
    {
        $form = $this->createForm(GiftSearchType::class);
        $form->handleRequest($request);

        return $this->render('main/giftsearch.html.twig', [
            'searchForm' => $form->createView()
        ]);
    }

    #[Route('/giftsearch/results', name: 'app_gift_search_results', methods: ['POST'])]
    public function searchResults(Request $request, GiftRepository $giftRepository): JsonResponse
    {
        $form = $this->createForm(GiftSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gifts = $giftRepository->search($form->getData());

            $data = array_map(function (Gift $gift) {
                return [
                    'id' => $gift->getId(),
                    'name' => $gift->getName(),
                    'description' => $gift->getDescription(),
                    'price' => number_format($gift->getPrice(), 2) . ' €',
                    'gender' => $gift->getGender(),
                    'age' => $gift->getAgeGroup(),
                    'interests' => $gift->getInterest(),
                    'icon' => $gift->getIconClass() // assuming this field/method exists
                ];
            }, $gifts);

            return new JsonResponse($data);
        }

        return new JsonResponse(['error' => 'Invalid form submission'], 400);
    }
}
