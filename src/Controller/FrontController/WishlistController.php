<?php
namespace App\Controller\FrontController;

use App\Entity\Wishlist;
use App\Form\WishlistType;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/wishlist')]
#[IsGranted('ROLE_USER')]
class WishlistController extends AbstractController
{
    #[Route('', name: 'app_wishlist_index')]
    public function index(WishlistRepository $wishlistRepository): Response
    {
        return $this->render('wishlist/index.html.twig', [
            'wishlists' => $wishlistRepository->findByUser($this->getUser())
        ]);
    }

    #[Route('/new', name: 'app_wishlist_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $wishlist = new Wishlist();
        $wishlist->setUser($this->getUser());
        
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($wishlist);
            $em->flush();

            return $this->redirectToRoute('app_wishlist_index');
        }

        return $this->render('wishlist/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'app_wishlist_show')]
    public function show(Wishlist $wishlist): Response
    {
        $this->denyAccessUnlessGranted('VIEW', $wishlist);
        
        return $this->render('wishlist/show.html.twig', [
            'wishlist' => $wishlist
        ]);
    }

    #[Route('/{id}/edit', name: 'app_wishlist_edit')]
    public function edit(Request $request, Wishlist $wishlist, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $wishlist);
        
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_wishlist_index');
        }

        return $this->render('wishlist/edit.html.twig', [
            'wishlist' => $wishlist,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/delete', name: 'app_wishlist_delete')]
    public function delete(Request $request, Wishlist $wishlist, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $wishlist);
        
        if ($this->isCsrfTokenValid('delete'.$wishlist->getId(), $request->request->get('_token'))) {
            $em->remove($wishlist);
            $em->flush();
        }

        return $this->redirectToRoute('app_wishlist_index');
    }
}