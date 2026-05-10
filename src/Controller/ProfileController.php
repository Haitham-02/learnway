<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $errors = [];

        if ($request->isMethod('POST')) {
            $phone = trim($request->request->get('phone', ''));
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');

            $user->setPhone($phone);

            // Handle Password Update
            if (!empty($newPassword)) {
                if ($newPassword !== $confirmPassword) {
                    $errors[] = 'Passwords do not match.';
                } elseif (strlen($newPassword) < 6) {
                    $errors[] = 'Password must be at least 6 characters.';
                } else {
                    $user->setPassword(
                        $passwordHasher->hashPassword($user, $newPassword)
                    );
                }
            }

            // Handle Profile Picture Upload
            /** @var UploadedFile $profilePictureFile */
            $profilePictureFile = $request->files->get('profile_picture');
            if ($profilePictureFile) {
                $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profilePictureFile->guessExtension();

                try {
                    $profilePictureFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/profiles',
                        $newFilename
                    );
                    
                    // Delete old profile picture if exists
                    if ($user->getProfile_picture()) {
                        $oldFilePath = $this->getParameter('kernel.project_dir') . '/public/uploads/profiles/' . $user->getProfile_picture();
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                    
                    $user->setProfile_picture($newFilename);
                } catch (FileException $e) {
                    $errors[] = 'Could not upload profile picture.';
                }
            }

            if (empty($errors)) {
                $em->flush();
                $this->addFlash('success', 'Profile updated successfully.');
                return $this->redirectToRoute('app_profile_edit');
            }
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'errors' => $errors,
        ]);
    }
}
