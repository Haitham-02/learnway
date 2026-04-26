<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    #[Route('/users', name: 'users_index')]
    public function usersIndex(UserRepository $userRepository): Response
    {
        return $this->render('admin/users/index.html.twig', [
            'users' => $userRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/users/{id}/edit', name: 'users_edit', methods: ['GET', 'POST'])]
    public function usersEdit(
        int $id,
        Request $request,
        RoleRepository $roleRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        UserRepository $userRepository,
    ): Response {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException("User #{$id} not found.");
        }

        $roles = $roleRepository->findAll();
        $errors = [];

        if ($request->isMethod('POST')) {
            $email = trim($request->request->get('email', ''));
            $firstName = trim($request->request->get('first_name', ''));
            $lastName = trim($request->request->get('last_name', ''));
            $plainPassword = $request->request->get('password', '');
            $roleId = (int) $request->request->get('role_id');
            $isActive = (bool) $request->request->get('is_active', false);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email address is required.';
            } elseif ($email !== $user->getEmail() && $userRepository->findOneBy(['email' => $email])) {
                $errors[] = "Email «{$email}» is already in use.";
            }
            if ($firstName === '') {
                $errors[] = 'First name is required.';
            }
            if ($lastName === '') {
                $errors[] = 'Last name is required.';
            }
            if ($plainPassword !== '' && strlen($plainPassword) < 8) {
                $errors[] = 'New password must be at least 8 characters.';
            }
            $role = $roleId ? $roleRepository->find($roleId) : null;
            if (!$role) {
                $errors[] = 'Please select a valid role.';
            }

            if (empty($errors)) {
                $user->setEmail($email);
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setRole($role);
                $user->setIsActive($isActive);
                if ($plainPassword !== '') {
                    $user->setPasswordHash($hasher->hashPassword($user, $plainPassword));
                }

                $em->flush();

                $this->addFlash('success', "User «{$email}» updated successfully.");
                return $this->redirectToRoute('admin_users_index');
            }
        }

        return $this->render('admin/users/edit.html.twig', [
            'user' => $user,
            'roles' => $roles,
            'errors' => $errors,
        ]);
    }

    #[Route('/users/{id}/delete', name: 'users_delete', methods: ['POST'])]
    public function usersDelete(
        int $id,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
    ): Response {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException("User #{$id} not found.");
        }

        if ($user->getId() === $this->getUser()?->getId()) {
            $this->addFlash('error', 'You cannot delete your own account.');
            return $this->redirectToRoute('admin_users_index');
        }

        if ($this->isCsrfTokenValid('delete_user_' . $id, $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', "User «{$user->getEmail()}» deleted.");
        }

        return $this->redirectToRoute('admin_users_index');
    }

    #[Route('/users/new', name: 'users_new', methods: ['GET', 'POST'])]
    public function usersNew(
        Request $request,
        RoleRepository $roleRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        UserRepository $userRepository,
    ): Response {
        $roles = $roleRepository->findAll();
        $errors = [];

        if ($request->isMethod('POST')) {
            $email = trim($request->request->get('email', ''));
            $firstName = trim($request->request->get('first_name', ''));
            $lastName = trim($request->request->get('last_name', ''));
            $plainPassword = $request->request->get('password', '');
            $roleId = (int) $request->request->get('role_id');
            $isActive = (bool) $request->request->get('is_active', false);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email address is required.';
            } elseif ($userRepository->findOneBy(['email' => $email])) {
                $errors[] = "Email «{$email}» is already in use.";
            }
            if ($firstName === '') {
                $errors[] = 'First name is required.';
            }
            if ($lastName === '') {
                $errors[] = 'Last name is required.';
            }
            if (strlen($plainPassword) < 8) {
                $errors[] = 'Password must be at least 8 characters.';
            }
            $role = $roleId ? $roleRepository->find($roleId) : null;
            if (!$role) {
                $errors[] = 'Please select a valid role.';
            }

            if (empty($errors)) {
                $user = new User();
                $user->setEmail($email);
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setRole($role);
                $user->setIsActive($isActive);
                $user->setCreatedAt(new \DateTime());
                $user->setPasswordHash($hasher->hashPassword($user, $plainPassword));

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', "User «{$email}» created successfully.");
                return $this->redirectToRoute('admin_users_index');
            }
        }

        return $this->render('admin/users/new.html.twig', [
            'roles' => $roles,
            'errors' => $errors,
        ]);
    }
}
