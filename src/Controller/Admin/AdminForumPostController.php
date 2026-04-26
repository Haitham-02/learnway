<?php

namespace App\Controller\Admin;

use App\Entity\ForumPost;
use App\Repository\ClasseRepository;
use App\Repository\ForumPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminForumPostController extends AbstractController
{
    #[Route('/forum-posts', name: 'forum_posts_index')]
    public function forumPostsIndex(ForumPostRepository $forumPostRepository): Response
    {
        return $this->render('admin/forum_posts/index.html.twig', [
            'posts' => $forumPostRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/forum-posts/new', name: 'forum_posts_new', methods: ['GET', 'POST'])]
    public function forumPostsNew(
        Request $request,
        ClasseRepository $classeRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em,
    ): Response {
        $classes = $classeRepository->findBy([], ['name' => 'ASC']);
        $users = $userRepository->findBy([], ['id' => 'DESC']);
        $errors = [];

        if ($request->isMethod('POST')) {
            $classId = (int) $request->request->get('class_id');
            $userId = (int) $request->request->get('user_id');
            $title = trim($request->request->get('title', ''));
            $subtitle = trim($request->request->get('subtitle', ''));
            $description = trim($request->request->get('description', ''));
            $content = trim($request->request->get('content', ''));
            $featuredImage = trim($request->request->get('featured_image', ''));

            $classe = $classId ? $classeRepository->find($classId) : null;
            $user = $userId ? $userRepository->find($userId) : null;

            if (!$classe) {
                $errors[] = 'Please select a valid class.';
            }
            if (!$user) {
                $errors[] = 'Please select a valid author.';
            }
            if ($title === '') {
                $errors[] = 'Title is required.';
            }
            if ($content === '') {
                $errors[] = 'Content is required.';
            }

            if (empty($errors)) {
                $post = new ForumPost();
                $post->setClasse($classe);
                $post->setUser($user);
                $post->setTitle($title);
                $post->setSubtitle($subtitle !== '' ? $subtitle : null);
                $post->setDescription($description !== '' ? $description : null);
                $post->setContent($content);
                $post->setFeaturedImage($featuredImage !== '' ? $featuredImage : null);
                $post->setCreatedAt(new \DateTime());

                $em->persist($post);
                $em->flush();

                $this->addFlash('success', 'Forum post created successfully.');
                return $this->redirectToRoute('admin_forum_posts_index');
            }
        }

        return $this->render('admin/forum_posts/new.html.twig', [
            'classes' => $classes,
            'users' => $users,
            'errors' => $errors,
        ]);
    }

    #[Route('/forum-posts/{id}/edit', name: 'forum_posts_edit', methods: ['GET', 'POST'])]
    public function forumPostsEdit(
        int $id,
        Request $request,
        ForumPostRepository $forumPostRepository,
        ClasseRepository $classeRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em,
    ): Response {
        $post = $forumPostRepository->find($id);
        if (!$post) {
            throw $this->createNotFoundException("Forum post #{$id} not found.");
        }

        $classes = $classeRepository->findBy([], ['name' => 'ASC']);
        $users = $userRepository->findBy([], ['id' => 'DESC']);
        $errors = [];

        if ($request->isMethod('POST')) {
            $classId = (int) $request->request->get('class_id');
            $userId = (int) $request->request->get('user_id');
            $title = trim($request->request->get('title', ''));
            $subtitle = trim($request->request->get('subtitle', ''));
            $description = trim($request->request->get('description', ''));
            $content = trim($request->request->get('content', ''));
            $featuredImage = trim($request->request->get('featured_image', ''));

            $classe = $classId ? $classeRepository->find($classId) : null;
            $user = $userId ? $userRepository->find($userId) : null;

            if (!$classe) {
                $errors[] = 'Please select a valid class.';
            }
            if (!$user) {
                $errors[] = 'Please select a valid author.';
            }
            if ($title === '') {
                $errors[] = 'Title is required.';
            }
            if ($content === '') {
                $errors[] = 'Content is required.';
            }

            if (empty($errors)) {
                $post->setClasse($classe);
                $post->setUser($user);
                $post->setTitle($title);
                $post->setSubtitle($subtitle !== '' ? $subtitle : null);
                $post->setDescription($description !== '' ? $description : null);
                $post->setContent($content);
                $post->setFeaturedImage($featuredImage !== '' ? $featuredImage : null);

                $em->flush();

                $this->addFlash('success', 'Forum post updated successfully.');
                return $this->redirectToRoute('admin_forum_posts_index');
            }
        }

        return $this->render('admin/forum_posts/edit.html.twig', [
            'post' => $post,
            'classes' => $classes,
            'users' => $users,
            'errors' => $errors,
        ]);
    }

    #[Route('/forum-posts/{id}/delete', name: 'forum_posts_delete', methods: ['POST'])]
    public function forumPostsDelete(
        int $id,
        Request $request,
        ForumPostRepository $forumPostRepository,
        EntityManagerInterface $em,
    ): Response {
        $post = $forumPostRepository->find($id);
        if (!$post) {
            throw $this->createNotFoundException("Forum post #{$id} not found.");
        }

        if ($this->isCsrfTokenValid('delete_forum_post_' . $id, $request->request->get('_token'))) {
            $em->remove($post);
            $em->flush();
            $this->addFlash('success', 'Forum post deleted.');
        }

        return $this->redirectToRoute('admin_forum_posts_index');
    }
}
