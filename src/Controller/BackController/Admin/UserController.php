<?php
namespace App\Controller\BackController\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('', name: 'admin_user_index')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $userCount = count($users);
        $emails = array_map(fn($u) => $u->getEmail(), $users);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'userCount' => $userCount,
            'emails' => $emails,
        ]);
    }

    #[Route('/{id}', name: 'admin_user_show', requirements: ['id' => '\d+'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_user_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('edit'.$user->getId(), $request->request->get('_token'))) {
            $newRoles = $request->request->get('roles');
            
            if (is_array($newRoles)) {
                $user->setRoles($newRoles);
                $em->flush();
                $this->addFlash('success', 'User roles updated');
            }
        }

        return $this->redirectToRoute('admin_user_show', ['id' => $user->getId()]);
    }

    #[Route('/add', name: 'admin_user_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $roles = $request->request->all('roles');
        $name = $request->request->get('name'); // Add this line

        if (empty($roles)) {
            $roles = ['ROLE_USER'];
        }

        if ($email && $password && $name) { // Also check for name
            // Check if user with this email already exists
            $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('danger', 'Un utilisateur avec cet email existe déjà.');
            } else {
                $user = new User();
                $user->setEmail($email);
                $user->setRoles($roles);
                $user->setPassword($passwordHasher->hashPassword($user, $password));
                $user->setName($name); // Set the name
                $user->setIsAdmin(true);
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Utilisateur ajouté !');
            }
        } else {
            $this->addFlash('danger', 'Email, nom et mot de passe requis.');
        }

        // Redirect to the user list, not to the add route
        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/delete/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur supprimé !');
        }
        return $this->redirectToRoute('admin_user_index');
    }
}
