<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/notifications')]
#[IsGranted('ROLE_USER')]
class NotificationController extends AbstractController
{
    public function __construct(
        private NotificationRepository $notificationRepo,
        private NotificationService $notificationService
    ) {}

    #[Route('/', name: 'notifications_index')]
    public function index(): Response
    {
        $user = $this->getAuthenticatedUser();
        $notifications = $this->notificationRepo->findAllForUser($user);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/unread-count', name: 'notifications_unread_count')]
    public function unreadCount(): Response
    {
        $user = $this->getAuthenticatedUser();
        $count = $this->notificationRepo->countUnreadForUser($user);

        return new Response((string) $count);
    }

    #[Route('/latest', name: 'notifications_latest')]
    public function latest(): Response
    {
        $user = $this->getAuthenticatedUser();
        $notifications = $this->notificationRepo->findUnreadForUser($user, 5);

        return $this->render('notification/_latest.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/mark-as-read/{id}', name: 'notifications_mark_as_read', methods: ['POST'])]
    public function markAsRead(Notification $notification): JsonResponse
    {
        if ($notification->getUser() !== $this->getAuthenticatedUser()) {
            return new JsonResponse(['success' => false], Response::HTTP_NOT_FOUND);
        }

        $this->notificationService->markAsRead($notification);

        return new JsonResponse(['success' => true]);
    }

    #[Route('/mark-all-read', name: 'notifications_mark_all_read', methods: ['POST'])]
    public function markAllRead(): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
        $this->notificationService->markAllAsRead($user);

        return new JsonResponse(['success' => true]);
    }

    private function getAuthenticatedUser(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $user;
    }
}
