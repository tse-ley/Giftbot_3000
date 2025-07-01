<?php

namespace App\Controller\FrontController;

use App\Entity\NewsLetter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NewsLetterRepository;

class NewsletterController extends AbstractController
{
    #[Route('/newsletter', name: 'app_newsletter')]
    public function subscribe(
        Request $request,
        EntityManagerInterface $em,
        NewsLetterRepository $newsletterRepository
    ): Response {
        $email = $request->request->get('email');

        if ($email) {
            $newsLetter = new NewsLetter();
            $newsLetter->setEmail($email);
            $newsLetter->setSubscribedAt(new \DateTimeImmutable());

            $newsletterRepository->add($newsLetter, true);

            $this->addFlash('succès', 'Vous êtes inscrit à notre newsletter !');
            return $this->redirectToRoute('app_home');
        }

        return $this->redirectToRoute('app_home');
    }
}
