<?php
namespace App\Controller\BackController\Admin;

use App\Entity\NewsLetter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NewsLetterRepository;

class NewsletterController extends AbstractController
{
    #[Route('/admin/newsletter', name: 'admin_news_letter_index')]
    public function index(NewsLetterRepository $newsletterRepo): Response
    {
        $subscribers = $newsletterRepo->findAll();

        return $this->render('admin/news_letter/index.html.twig', [
            'subscribers' => $subscribers,
        ]);
    }

    #[Route('/newsletter', name: 'app_newsletter')]
    public function subscribe(
        Request $request,
        EntityManagerInterface $em,
        NewsLetterRepository $newsletterRepo
    ): Response {
        $email = $request->request->get('email');

        if ($email) {
            $newsLetter = new NewsLetter();
            $newsLetter->setEmail($email);
            $newsLetter->setSubscribedAt(new \DateTimeImmutable());

            $em->persist($newsLetter);
            $em->flush();
            
            $this->addFlash('success', 'You have been subscribed to our newsletter!');
            return $this->redirectToRoute('app_home');
        }

        return $this->redirectToRoute('app_home');
    }
}
