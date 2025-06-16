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
        $query = $request->query->get('q', '');

        $gifts = $query
            ? $giftRepository->createQueryBuilder('g')
                ->where('LOWER(g.name) LIKE :q OR LOWER(g.description) LIKE :q')
                ->setParameter('q', '%' . strtolower($query) . '%')
                ->getQuery()
                ->getResult()
            : $giftRepository->findAll();

        return $this->render('gift/gift.html.twig', [
            'gifts' => $gifts,
            'searchQuery' => $query,
        ]);
    }

    #[Route('/gifts/{id}', name: 'app_gift_show')]
    public function show(Gift $gift): Response
    {
        return $this->render('gift/show.html.twig', [
            'gift' => $gift,
        ]);
    }

    #[Route('/gifts/search', name: 'app_gift_search_results')]
    public function search(Request $request, GiftRepository $giftRepository): Response
    {
        $criteria = [
            'category' => $request->request->get('category'),
            'label' => $request->request->get('label'),
        ];

        $gifts = $giftRepository->search($criteria);

        $giftArray = array_map(function (Gift $gift) {
            return [
                'id' => $gift->getId(),
                'name' => $gift->getName(),
                'description' => $gift->getDescription(),
                'price' => $gift->getPrice(),
                'image' => $gift->getImage(),
                'category' => $gift->getCategory(),
                'label' => $gift->getLabel(),
            ];
        }, $gifts);

        return $this->json($giftArray);
    }
}
