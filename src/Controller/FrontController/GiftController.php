<?php

namespace App\Controller\FrontController;

use App\Entity\Gift;
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

        if ($query) {
            // for finding matching gifts
            $gifts = $giftRepository->createQueryBuilder('g')
                ->where('LOWER(g.name) LIKE :q OR LOWER(g.description) LIKE :q')
                ->setParameter('q', '%' . strtolower($query) . '%')
                ->orderBy('g.name', 'ASC')
                ->getQuery()
                ->getResult();
        } else {
            // If there is nothing is being searched, get all gifts.
            $gifts = $giftRepository->findAll();
        }

        return $this->render('gift/gift.html.twig', [
            'gifts' => $gifts, 
            'searchQuery' => $query,
        ]);
    }

    /**
     * Displays a single gift's detail page.
     * This uses Symfony's ParamConverter to automatically find the Gift by its ID.
    **/
    #[Route('/gifts/{id}', name: 'app_gift_show')]
    public function show(Gift $gift): Response
    {
        return $this->render('gift/show.html.twig', [
            'gift' => $gift,
        ]);
    }
}