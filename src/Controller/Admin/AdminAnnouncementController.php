<?php

namespace App\Controller\Admin;

use App\Entity\Announcement;
use App\Repository\AnnouncementRepository;
use App\Repository\ClasseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/announcements', name: 'admin_announcement_')]
#[IsGranted('ROLE_ADMIN')]
class AdminAnnouncementController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(AnnouncementRepository $announcementRepository): Response
    {
        return $this->render('admin/announcement/index.html.twig', [
            'announcements' => $announcementRepository->findBy([], ['publish_at' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $em,
        ClasseRepository $classeRepo
    ): Response {
        $errors = [];
        $gradeLevels = $this->getGradeLevels($classeRepo);
        $classes = $classeRepo->findAll();

        if ($request->isMethod('POST')) {
            $title = trim($request->request->get('title', ''));
            $content = trim($request->request->get('content', ''));
            $priority = $request->request->get('priority', 'NORMAL');
            $targetType = $request->request->get('target_type', 'SCHOOL');
            $targetValue = $request->request->get('target_value');
            $targetId = $request->request->get('target_id');
            $publishAtRaw = $request->request->get('publish_at');
            $expireAtRaw = $request->request->get('expire_at');
            $isPinned = (bool) $request->request->get('is_pinned', false);

            if ($title === '') $errors[] = 'Title is required.';
            if ($content === '') $errors[] = 'Content is required.';

            if (empty($errors)) {
                $announcement = new Announcement();
                $announcement->setTitle($title);
                $announcement->setContent($content);
                $announcement->setPriority($priority);
                $announcement->setTargetType($targetType);
                $announcement->setUser($this->getUser());
                $announcement->setIsPinned($isPinned);

                if ($targetType === 'GRADE') {
                    $announcement->setTargetValue($targetValue);
                } elseif ($targetType === 'CLASS') {
                    $announcement->setTargetId((int) $targetId);
                }

                if ($publishAtRaw) {
                    $announcement->setPublishAt(new \DateTime($publishAtRaw));
                } else {
                    $announcement->setPublishAt(new \DateTime());
                }

                if ($expireAtRaw) {
                    $announcement->setExpireAt(new \DateTime($expireAtRaw));
                }

                $em->persist($announcement);
                $em->flush();

                $this->addFlash('success', 'Announcement created successfully.');
                return $this->redirectToRoute('admin_announcement_index');
            }
        }

        return $this->render('admin/announcement/form.html.twig', [
            'errors' => $errors,
            'gradeLevels' => $gradeLevels,
            'classes' => $classes,
            'title' => 'Create Announcement',
            'announcement' => null,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request, 
        EntityManagerInterface $em,
        AnnouncementRepository $announcementRepo,
        ClasseRepository $classeRepo
    ): Response {
        $announcement = $announcementRepo->find($id);
        if (!$announcement) throw $this->createNotFoundException();

        $errors = [];
        $gradeLevels = $this->getGradeLevels($classeRepo);
        $classes = $classeRepo->findAll();

        if ($request->isMethod('POST')) {
            $title = trim($request->request->get('title', ''));
            $content = trim($request->request->get('content', ''));
            $priority = $request->request->get('priority', 'NORMAL');
            $targetType = $request->request->get('target_type', 'SCHOOL');
            $targetValue = $request->request->get('target_value');
            $targetId = $request->request->get('target_id');
            $publishAtRaw = $request->request->get('publish_at');
            $expireAtRaw = $request->request->get('expire_at');
            $isPinned = (bool) $request->request->get('is_pinned', false);

            if ($title === '') $errors[] = 'Title is required.';
            if ($content === '') $errors[] = 'Content is required.';

            if (empty($errors)) {
                $announcement->setTitle($title);
                $announcement->setContent($content);
                $announcement->setPriority($priority);
                $announcement->setTargetType($targetType);
                $announcement->setIsPinned($isPinned);

                if ($targetType === 'GRADE') {
                    $announcement->setTargetValue($targetValue);
                    $announcement->setTargetId(null);
                } elseif ($targetType === 'CLASS') {
                    $announcement->setTargetId((int) $targetId);
                    $announcement->setTargetValue(null);
                } else {
                    $announcement->setTargetId(null);
                    $announcement->setTargetValue(null);
                }

                if ($publishAtRaw) {
                    $announcement->setPublishAt(new \DateTime($publishAtRaw));
                }

                if ($expireAtRaw) {
                    $announcement->setExpireAt(new \DateTime($expireAtRaw));
                } else {
                    $announcement->setExpireAt(null);
                }

                $em->flush();

                $this->addFlash('success', 'Announcement updated successfully.');
                return $this->redirectToRoute('admin_announcement_index');
            }
        }

        return $this->render('admin/announcement/form.html.twig', [
            'errors' => $errors,
            'gradeLevels' => $gradeLevels,
            'classes' => $classes,
            'title' => 'Edit Announcement',
            'announcement' => $announcement,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Announcement $announcement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$announcement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($announcement);
            $entityManager->flush();
            $this->addFlash('success', 'Announcement deleted successfully.');
        }

        return $this->redirectToRoute('admin_announcement_index');
    }

    private function getGradeLevels(ClasseRepository $classeRepo): array
    {
        $gradeLevels = $classeRepo->createQueryBuilder('c')
            ->select('DISTINCT c.grade_level')
            ->orderBy('c.grade_level', 'ASC')
            ->getQuery()
            ->getResult();

        return array_filter(array_column($gradeLevels, 'grade_level'));
    }
}
