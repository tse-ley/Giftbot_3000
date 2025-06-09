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

        if ($query) {
            $gifts = $giftRepository->createQueryBuilder('g')
                ->where('LOWER(g.name) LIKE :q OR LOWER(g.description) LIKE :q')
                ->setParameter('q', '%' . strtolower($query) . '%')
                ->getQuery()
                ->getResult();
        } else {
            $gifts = $giftRepository->findAll();
        }

        return $this->render('gift/gift.html.twig', [
            'gifts' => $gifts,
            'searchQuery' => $query,
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