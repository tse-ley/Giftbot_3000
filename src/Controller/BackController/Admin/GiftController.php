<?php

namespace App\Controller\BackController\Admin;

use App\Repository\GiftRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Gift;
use App\Form\GiftType;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin/gift')]
class GiftController extends AbstractController
{
    #[Route('', name: 'admin_gift_index')]
    public function index(GiftRepository $giftRepository): Response
    {
        $gifts = $giftRepository->findAll();

        return $this->render('admin/gift/index.html.twig', [
            'gifts' => $gifts,
        ]);
    }

    #[Route('/add', name: 'gift_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $gift = new Gift();
        $form = $this->createForm(GiftType::class, $gift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($gift);
            $em->flush();

            $this->addFlash('success', 'Gift added!');
            return $this->redirectToRoute('admin_gift_index');
        }

        return $this->render('admin/gift/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'gift_delete')]
    public function delete(Gift $gift, EntityManagerInterface $em): Response
    {
        $em->remove($gift);
        $em->flush();

        $this->addFlash('success', 'Gift deleted!');
        return $this->redirectToRoute('admin_gift_index');
    }
}