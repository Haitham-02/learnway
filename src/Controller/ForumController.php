<?php

namespace App\Controller;

use App\Entity\ForumPost;
use App\Entity\ForumComment;
use App\Repository\ForumPostRepository;
use App\Repository\StudentEnrollmentRepository;
use App\Repository\TeacherAssignmentRepository;
use App\Service\ProfanityFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/forum', name: 'app_forum_')]
#[IsGranted('ROLE_USER')]
class ForumController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(ForumPostRepository $postRepo): Response
    {
        // Only show approved posts
        $posts = $postRepo->findBy(['status' => 'APPROVED'], ['created_at' => 'DESC']);

        return $this->render('forum/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_STUDENT') or is_granted('ROLE_TEACHER')"))]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        ProfanityFilterService $profanityFilter,
        StudentEnrollmentRepository $enrollmentRepo,
        TeacherAssignmentRepository $taRepo
    ): Response {
        $errors = [];
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $title = trim($request->request->get('title', ''));
            $content = trim($request->request->get('content', ''));
            
            if ($title === '') $errors[] = 'Title is required.';
            if ($content === '') $errors[] = 'Content is required.';

            if (empty($errors)) {
                $post = new ForumPost();
                $post->setTitle($title);
                $post->setContent($content);
                $post->setUser($user);
                $post->setCreatedAt(new \DateTime());
                $post->setStatus('PENDING'); // Default status

                // Automatic profanity filtering
                $post->setTitle($profanityFilter->filter($title));
                $post->setContent($profanityFilter->filter($content));

                // Associate with class if student
                if ($this->isGranted('ROLE_STUDENT')) {
                    $enrollment = $enrollmentRepo->findOneBy(['user' => $user], ['id' => 'DESC']);
                    if ($enrollment) {
                        $post->setClasse($enrollment->getClasse());
                    }
                }

                $em->persist($post);
                $em->flush();

                $this->addFlash('success', 'Your post has been submitted and is awaiting admin approval.');
                return $this->redirectToRoute('app_forum_index');
            }
        }

        return $this->render('forum/new.html.twig', [
            'errors' => $errors,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(ForumPost $post): Response
    {
        if ($post->getStatus() !== 'APPROVED' && !$this->isGranted('ROLE_ADMIN') && $post->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('This post is not approved yet.');
        }

        return $this->render('forum/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_STUDENT') or is_granted('ROLE_TEACHER')"))]
    public function edit(
        ForumPost $post,
        Request $request,
        EntityManagerInterface $em,
        ProfanityFilterService $profanityFilter
    ): Response {
        $user = $this->getUser();

        if ($post->getUser() !== $user) {
            throw $this->createAccessDeniedException('You can only edit your own posts.');
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            $title = trim($request->request->get('title', ''));
            $content = trim($request->request->get('content', ''));

            if ($title === '') $errors[] = 'Title is required.';
            if ($content === '') $errors[] = 'Content is required.';

            if (empty($errors)) {
                $post->setTitle($profanityFilter->filter($title));
                $post->setContent($profanityFilter->filter($content));
                $post->setStatus('PENDING');

                $em->flush();

                $this->addFlash('success', 'Your post has been updated and is awaiting re-approval.');
                return $this->redirectToRoute('app_forum_show', ['id' => $post->getId()]);
            }
        }

        return $this->render('forum/edit.html.twig', [
            'post' => $post,
            'errors' => $errors,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_STUDENT') or is_granted('ROLE_TEACHER')"))]
    public function delete(
        ForumPost $post,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        if ($post->getUser() !== $user) {
            throw $this->createAccessDeniedException('You can only delete your own posts.');
        }

        if ($this->isCsrfTokenValid('delete_forum_post_' . $post->getId(), $request->request->get('_token'))) {
            $em->remove($post);
            $em->flush();
            $this->addFlash('success', 'Your post has been deleted.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_forum_index');
    }

    #[Route('/{id}/comment', name: 'comment', methods: ['POST'])]
    public function comment(
        ForumPost $post,
        Request $request,
        EntityManagerInterface $em,
        ProfanityFilterService $profanityFilter
    ): Response {
        $content = trim($request->request->get('content', ''));
        if ($content === '') {
            $this->addFlash('error', 'Comment content cannot be empty.');
            return $this->redirectToRoute('app_forum_show', ['id' => $post->getId()]);
        }

        $comment = new ForumComment();
        $comment->setForumPost($post);
        $comment->setContent($profanityFilter->filter($content));
        $comment->setUser($this->getUser());
        $comment->setCreatedAt(new \DateTime());
        $comment->setStatus('APPROVED'); // Comments are approved by default after filtering

        $em->persist($comment);
        $em->flush();

        $this->addFlash('success', 'Your comment has been posted.');
        return $this->redirectToRoute('app_forum_show', ['id' => $post->getId()]);
    }
}
