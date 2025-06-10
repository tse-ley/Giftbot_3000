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

    #[Route('/gifts', name: 'app_gifts')]
    public function index(Request $request, GiftRepository $giftRepository): Response
    {
        $query = $request->query->get('q', '');
        $category = $request->query->get('category', '');
        $label = $request->query->get('label', '');
        $age = $request->query->get('age', '');

        $qb = $giftRepository->createQueryBuilder('g');

        // Age logic
        if ($age === '0-5' || $age === '13-17') {
            $qb->andWhere('LOWER(g.category) = :enfants')
               ->setParameter('enfants', 'enfants');
        } elseif ($category === 'animaux' || $age === 'animaux') {
            $qb->andWhere('LOWER(g.category) = :animaux')
               ->setParameter('animaux', 'animaux');
        } elseif ($category) {
            $qb->andWhere('LOWER(g.category) = :category')
               ->setParameter('category', strtolower($category));
        }

        if ($label) {
            $qb->andWhere('LOWER(g.label) = :label')
               ->setParameter('label', strtolower($label));
        }

        if ($query) {
            $qb->andWhere('LOWER(g.name) LIKE :q OR LOWER(g.description) LIKE :q')
               ->setParameter('q', '%' . strtolower($query) . '%');
        }

        $gifts = $qb->getQuery()->getResult();

        return $this->render('gift/gift.html.twig', [
            'gifts' => $gifts,
            'searchQuery' => $query,
            'selectedCategory' => $category,
            'selectedLabel' => $label,
            'selectedAge' => $age,
        ]);
    }
}
