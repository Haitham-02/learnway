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
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    #[IsGranted(new Expression("is_granted('ROLE_STUDENT') or is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')"))]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        ProfanityFilterService $profanityFilter,
        StudentEnrollmentRepository $enrollmentRepo,
        TeacherAssignmentRepository $taRepo,
        SluggerInterface $slugger,
        \App\Repository\UserRepository $userRepo
    ): Response {
        $errors = [];
        $user = $this->getUser();
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $allUsers = $isAdmin ? $userRepo->findAll() : [];

        if ($request->isMethod('POST')) {
            $title = trim($request->request->get('title', ''));
            $content = trim($request->request->get('content', ''));
            
            if ($title === '') $errors[] = 'Title is required.';
            if ($content === '') $errors[] = 'Content is required.';

            if (empty($errors)) {
                $author = $user;
                if ($isAdmin && $request->request->get('author_id')) {
                    $selectedUser = $userRepo->find($request->request->get('author_id'));
                    if ($selectedUser) {
                        $author = $selectedUser;
                    }
                }
                
                $post = new ForumPost();
                $post->setTitle($title);
                $post->setContent($content);
                $post->setUser($author);
                $post->setCreatedAt(new \DateTime());
                $post->setStatus($isAdmin ? 'APPROVED' : 'PENDING');

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

                // Handle File Upload
                /** @var UploadedFile $imageFile */
                $imageFile = $request->files->get('featured_image');
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads/forum',
                            $newFilename
                        );
                        $post->setFeaturedImage($newFilename);
                    } catch (FileException $e) {
                        // ... handle exception if needed
                    }
                }

                $em->persist($post);
                $em->flush();

                if ($isAdmin) {
                    $this->addFlash('success', 'The post has been published successfully.');
                } else {
                    $this->addFlash('success', 'Your post has been submitted and is awaiting admin approval.');
                }
                return $this->redirectToRoute('app_forum_index');
            }
        }

        return $this->render('forum/new.html.twig', [
            'errors' => $errors,
            'allUsers' => $allUsers,
            'isAdmin' => $isAdmin,
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
    #[IsGranted(new Expression("is_granted('ROLE_STUDENT') or is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')"))]
    public function edit(
        ForumPost $post,
        Request $request,
        EntityManagerInterface $em,
        ProfanityFilterService $profanityFilter,
        SluggerInterface $slugger
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

                // Handle File Upload
                /** @var UploadedFile $imageFile */
                $imageFile = $request->files->get('featured_image');
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads/forum',
                            $newFilename
                        );
                        // Delete old image if it exists
                        if ($post->getFeaturedImage()) {
                            $oldFilePath = $this->getParameter('kernel.project_dir') . '/public/uploads/forum/' . $post->getFeaturedImage();
                            if (file_exists($oldFilePath)) {
                                unlink($oldFilePath);
                            }
                        }
                        $post->setFeaturedImage($newFilename);
                    } catch (FileException $e) {
                        // ... handle exception if needed
                    }
                }

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
    #[IsGranted(new Expression("is_granted('ROLE_STUDENT') or is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')"))]
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
        $parentId = $request->request->get('parent_id');

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

        if ($parentId) {
            $parent = $em->getRepository(ForumComment::class)->find($parentId);
            if ($parent && $parent->getForumPost() === $post) {
                $comment->setForumComment($parent);
            }
        }

        $em->persist($comment);
        $em->flush();

        $this->addFlash('success', 'Your comment has been posted.');
        return $this->redirectToRoute('app_forum_show', ['id' => $post->getId()]);
    }

    #[Route('/comment/{id}/edit', name: 'comment_edit', methods: ['POST'])]
    public function editComment(
        ForumComment $comment,
        Request $request,
        EntityManagerInterface $em,
        ProfanityFilterService $profanityFilter
    ): Response {
        if ($comment->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You can only edit your own comments.');
        }

        $content = trim($request->request->get('content', ''));
        if ($content !== '') {
            $comment->setContent($profanityFilter->filter($content));
            $em->flush();
            $this->addFlash('success', 'Comment updated successfully.');
        }

        return $this->redirectToRoute('app_forum_show', ['id' => $comment->getForumPost()->getId()]);
    }

    #[Route('/comment/{id}/delete', name: 'comment_delete', methods: ['POST'])]
    public function deleteComment(
        ForumComment $comment,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($comment->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You can only delete your own comments.');
        }

        $postId = $comment->getForumPost()->getId();

        if ($this->isCsrfTokenValid('delete_comment_' . $comment->getId(), $request->request->get('_token'))) {
            $this->removeCommentRecursively($em, $comment);
            $em->flush();
            $this->addFlash('success', 'Comment deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_forum_show', ['id' => $postId]);
    }

    private function removeCommentRecursively(EntityManagerInterface $em, ForumComment $comment): void
    {
        foreach ($comment->getForumComments() as $child) {
            $this->removeCommentRecursively($em, $child);
        }
        $em->remove($comment);
    }

    #[Route('/{id}/review', name: 'review', methods: ['POST'])]
    public function review(
        ForumPost $post,
        Request $request,
        EntityManagerInterface $em,
        ProfanityFilterService $profanityFilter
    ): Response {
        $user = $this->getUser();
        $rating = (int) $request->request->get('rating', 0);
        $reviewText = trim($request->request->get('review_text', ''));

        if ($rating < 1 || $rating > 5) {
            $this->addFlash('error', 'Please provide a valid rating between 1 and 5 stars.');
            return $this->redirectToRoute('app_forum_show', ['id' => $post->getId()]);
        }

        $existingReview = $em->getRepository(\App\Entity\ForumReview::class)->findOneBy([
            'forumPost' => $post,
            'user' => $user
        ]);

        if ($existingReview) {
            $existingReview->setRating($rating);
            $existingReview->setReview_text($profanityFilter->filter($reviewText));
            $this->addFlash('success', 'Your review has been updated.');
        } else {
            $review = new \App\Entity\ForumReview();
            $review->setForumPost($post);
            $review->setUser($user);
            $review->setRating($rating);
            $review->setReview_text($profanityFilter->filter($reviewText));
            $review->setCreated_at(new \DateTime());
            $em->persist($review);
            $this->addFlash('success', 'Your review has been submitted.');
        }

        try {
            $em->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while saving your review.');
        }

        return $this->redirectToRoute('app_forum_show', ['id' => $post->getId()]);
    }
}
