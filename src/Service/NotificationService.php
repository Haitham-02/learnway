<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function send(User $user, string $title, string $message, ?string $link = null, bool $flush = true): Notification
    {
        $notification = $this->createNotification($user, $title, $message, $link);
        $this->entityManager->persist($notification);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $notification;
    }

    /**
     * @param iterable<User> $users
     */
    public function sendToUsers(iterable $users, string $title, string $message, ?string $link = null): int
    {
        $uniqueUsers = [];
        foreach ($users as $user) {
            if ($user->getId() === null) {
                continue;
            }
            $uniqueUsers[$user->getId()] = $user;
        }

        foreach ($uniqueUsers as $user) {
            $this->entityManager->persist($this->createNotification($user, $title, $message, $link));
        }

        if ($uniqueUsers !== []) {
            $this->entityManager->flush();
        }

        return count($uniqueUsers);
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->setIsRead(true);
        $this->entityManager->flush();
    }

    public function markAllAsRead(User $user): void
    {
        $this->entityManager->createQueryBuilder()
            ->update(Notification::class, 'n')
            ->set('n.isRead', ':isRead')
            ->where('n.user = :user')
            ->setParameter('isRead', true)
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    private function createNotification(User $user, string $title, string $message, ?string $link = null): Notification
    {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setTitle($title);
        $notification->setMessage($message);
        $notification->setLink($link);

        return $notification;
    }
}
