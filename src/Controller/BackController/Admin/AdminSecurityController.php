<?php
namespace App\Controller\BackController\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminSecurityController extends AbstractController
{
    #[Route('/login', name: 'admin_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        dump('AdminSecurityController::login() called');

        // If user is already authenticated, redirect to admin dashboard
        if ($this->getUser()) {
            dump('User is authenticated:', $this->getUser());
            return $this->redirectToRoute('admin_dashboard', ['slash' => '/']);
        }

        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        dump('Login error:', $error);
        dump('Last username:', $lastUsername);

        $response = $this->render('admin/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);

        dump('Rendering login form with last_username:', $lastUsername, 'and error:', $error);
        return $response;
    }

    #[Route('/logout', name: 'admin_logout')]
    public function logout(): void
    {
        // This method can be blank - it will be intercepted by the logout key on your firewall
    }
}
