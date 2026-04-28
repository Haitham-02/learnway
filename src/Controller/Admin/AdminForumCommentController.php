<?php

namespace App\Controller\Admin;

use App\Repository\ForumCommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminForumCommentController extends AbstractController
{
    #[Route('/forum-comments', name: 'forum_comments_index')]
    public function index(ForumCommentRepository $repo): Response
    {
        return $this->render('admin/forum_comments/index.html.twig', [
            'comments' => $repo->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/forum-comments/{id}/approve', name: 'forum_comments_approve', methods: ['POST'])]
    public function approve(int $id, ForumCommentRepository $repo, EntityManagerInterface $em): Response
    {
        $comment = $repo->find($id);
        if (!$comment) throw $this->createNotFoundException();

        $comment->setStatus('APPROVED');
        $em->flush();

        $this->addFlash('success', 'Comment approved.');
        return $this->redirectToRoute('admin_forum_comments_index');
    }

    #[Route('/forum-comments/{id}/reject', name: 'forum_comments_reject', methods: ['POST'])]
    public function reject(int $id, ForumCommentRepository $repo, EntityManagerInterface $em): Response
    {
        $comment = $repo->find($id);
        if (!$comment) throw $this->createNotFoundException();

        $comment->setStatus('REJECTED');
        $em->flush();

        $this->addFlash('success', 'Comment rejected.');
        return $this->redirectToRoute('admin_forum_comments_index');
    }
}
