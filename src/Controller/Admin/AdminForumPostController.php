<?php

namespace App\Controller\Admin;

use App\Entity\ForumPost;
use App\Repository\ForumPostRepository;
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

    #[Route('/forum-posts/{id}', name: 'forum_posts_show')]
    public function show(ForumPost $post): Response
    {
        return $this->render('admin/forum_posts/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/forum-posts/{id}/approve', name: 'forum_posts_approve', methods: ['POST'])]
    public function approve(int $id, ForumPostRepository $repo, EntityManagerInterface $em): Response
    {
        $post = $repo->find($id);
        if (!$post) throw $this->createNotFoundException();

        if ($post->getStatus() === 'APPROVED') {
            $this->addFlash('warning', 'This forum post is already approved.');
            return $this->redirectToRoute('admin_forum_posts_index');
        }

        $post->setStatus('APPROVED');
        $em->flush();

        $this->addFlash('success', 'Forum post approved.');
        return $this->redirectToRoute('admin_forum_posts_index');
    }

    #[Route('/forum-posts/{id}/reject', name: 'forum_posts_reject', methods: ['POST'])]
    public function reject(int $id, ForumPostRepository $repo, EntityManagerInterface $em): Response
    {
        $post = $repo->find($id);
        if (!$post) throw $this->createNotFoundException();

        if ($post->getStatus() === 'REJECTED') {
            $this->addFlash('warning', 'This forum post is already rejected.');
            return $this->redirectToRoute('admin_forum_posts_index');
        }

        $post->setStatus('REJECTED');
        $em->flush();

        $this->addFlash('success', 'Forum post rejected.');
        return $this->redirectToRoute('admin_forum_posts_index');
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
