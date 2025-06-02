<?php

namespace App\Controller\BackController\Admin;

use App\Entity\Gift;
use App\Form\GiftType;
use App\Repository\GiftRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/gift')]
#[IsGranted('ROLE_ADMIN')]
class GiftController extends AbstractController
{
    #[Route('', name: 'admin_gift_index')]
    public function index(GiftRepository $giftRepository): Response
    {
        return $this->render('gift/index.html.twig', [
            'gifts' => $giftRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'admin_gift_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $gift = new Gift();
        $form = $this->createForm(GiftType::class, $gift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($gift);
            $em->flush();

            $this->addFlash('success', 'Gift created successfully');
            return $this->redirectToRoute('admin_gift_index');
        }

        return $this->render('admin/gift/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'admin_gift_show')]
    public function show(Gift $gift): Response
    {
        return $this->render('admin/gift/show.html.twig', [
            'gift' => $gift
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_gift_edit')]
    public function edit(Request $request, Gift $gift, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(GiftType::class, $gift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Gift updated successfully');
            return $this->redirectToRoute('admin_gift_index');
        }

        return $this->render('admin/gift/edit.html.twig', [
            'gift' => $gift,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_gift_delete')]
    public function delete(Request $request, Gift $gift, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gift->getId(), $request->request->get('_token'))) {
            $em->remove($gift);
            $em->flush();
            $this->addFlash('success', 'Gift deleted successfully');
        }

        return $this->redirectToRoute('admin_gift_index');
    }
}