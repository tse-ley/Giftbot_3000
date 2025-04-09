<?php
namespace App\Controller;

use App\Entity\NewsletterSubscription;
use App\Form\NewsletterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    #[Route('/newsletter', name: 'app_newsletter')]
    public function subscribe(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $subscription = new NewsletterSubscription();
        $form = $this->createForm(NewsletterType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($subscription);
            $em->flush();
            
            $this->addFlash('success', 'You have been subscribed to our newsletter!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('newsletter/subscribe.html.twig', [
            'form' => $form->createView()
        ]);
    }
}