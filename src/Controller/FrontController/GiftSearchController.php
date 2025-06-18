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
     public function search(Request $request, GiftRepository $giftRepository): JsonResponse
    {
        $form = $this->createForm(GiftSearchType::class);
        $form->handleRequest($request);

        $gifts = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $criteria = $form->getData(); // Gets a clean array like ['category' => 'homme']

            // Call the search method in your repository
            $gifts = $giftRepository->search($criteria);
        }

        // Return the results as JSON, using the 'gift:read' group we defined in the Gift entity.
        // This is the correct, modern Symfony way.
        return $this->json($gifts, 200, [], ['groups' => 'gift:read']);
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
