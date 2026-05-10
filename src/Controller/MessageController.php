<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\ConversationMember;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Repository\StudentEnrollmentRepository;
use App\Repository\TeacherAssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Predis\Client as RedisClient;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/messages', name: 'app_messages_')]
#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(ConversationRepository $conversationRepo): Response
    {
        $user = $this->getUser();
        $conversations = $conversationRepo->findConversationsForUser($user->getId());

        return $this->render('message/index.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    #[Route('/start/{userId}', name: 'start')]
    public function start(
        #[MapEntity(id: 'userId')] User $recipient,
        ConversationRepository $conversationRepo,
        EntityManagerInterface $em,
        StudentEnrollmentRepository $enrollmentRepo,
        TeacherAssignmentRepository $taRepo
    ): Response {
        $currentUser = $this->getUser();

        if ($currentUser === $recipient) {
            $this->addFlash('error', 'You cannot message yourself.');
            return $this->redirectToRoute('app_messages_index');
        }

        // --- PERMISSION CHECKS ---
        $canMessage = $this->checkMessagingPermission($currentUser, $recipient, $enrollmentRepo, $taRepo);

        if (!$canMessage) {
            $this->addFlash('error', 'You do not have permission to message this user.');
            return $this->redirectToRoute('app_messages_index');
        }

        // Check if conversation already exists
        $conversation = $conversationRepo->findPeerToPeerConversation($currentUser->getId(), $recipient->getId());

        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->setType('DIRECT');
            $conversation->setCreatedAt(new \DateTime());
            
            // Create hash for peer-to-peer
            $ids = [$currentUser->getId(), $recipient->getId()];
            sort($ids);
            $conversation->setPairHash(md5(implode('_', $ids)));

            // Add members
            $member1 = new ConversationMember();
            $member1->setUser($currentUser);
            $member1->setJoinedAt(new \DateTime());
            $conversation->addMember($member1);

            $member2 = new ConversationMember();
            $member2->setUser($recipient);
            $member2->setJoinedAt(new \DateTime());
            $conversation->addMember($member2);

            $em->persist($conversation);
            $em->flush();
        }

        return $this->redirectToRoute('app_messages_show', ['id' => $conversation->getId()]);
    }

    #[Route('/contact-admin', name: 'contact_admin')]
    public function contactAdmin(UserRepository $userRepo): Response
    {
        $admin = $userRepo->findOneByRole('Admin');
        if (!$admin) {
            $this->addFlash('error', 'No administrator found.');
            return $this->redirectToRoute('app_messages_index');
        }

        return $this->redirectToRoute('app_messages_start', ['userId' => $admin->getId()]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET', 'POST'])]
    public function show(
        Conversation $conversation,
        Request $request,
        EntityManagerInterface $em,
        MessageRepository $messageRepo
    ): Response {
        // Connect to Redis - use 'redis' hostname when running in Docker, otherwise localhost
        $redisHost = $_ENV['REDIS_HOST'] ?? 'localhost';
        $redis = new RedisClient(['host' => $redisHost]);
        
        // Debug logging
        error_log("Redis connection: host={$redisHost}");
        
        $user = $this->getUser();
        
        // Ensure user is a member
        $isMember = false;
        foreach ($conversation->getMembers() as $member) {
            if ($member->getUser() === $user) {
                $isMember = true;
                break;
            }
        }

        if (!$isMember) {
            throw $this->createAccessDeniedException('You are not a member of this conversation.');
        }

        // Debug request method
        error_log("=== MESSAGE REQUEST ===");
        error_log("Method: " . $request->getMethod());
        error_log("Is POST: " . ($request->isMethod('POST') ? 'YES' : 'NO'));
        error_log("POST content: " . json_encode($request->request->all()));
        error_log("QUERY string: " . $request->getQueryString());

        if ($request->isMethod('POST')) {
            error_log("✓ Processing POST request");
            $content = trim($request->request->get('content', ''));
            error_log("Message content: '{$content}'");
            if ($content !== '') {
                error_log("Creating new message entity");
                $message = new Message();
                $message->setConversation($conversation);
                $message->setUser($user);
                $message->setContent($content);
                $message->setSentAt(new \DateTime());
                $message->setStatus('SENT');

                $em->persist($message);
                $em->flush();
                
                error_log("✓ Message saved to DB: ID=" . $message->getId());

                // Publish to Redis for real-time update
                try {
                    $messageData = json_encode([
                        'room' => 'conversation_' . $conversation->getId(),
                        'html' => $this->renderView('message/_message.html.twig', [
                            'message' => $message,
                            'forceTheirs' => true,
                        ])
                    ]);
                    $redis->publish('chat-messages', $messageData);
                    error_log("✓ Message published to Redis for conversation {$conversation->getId()}");
                } catch (\Exception $e) {
                    error_log("✗ Redis publish failed: " . $e->getMessage());
                }

                if ($request->headers->has('HX-Request')) {
                    error_log("✓ HTMX request detected, returning partial response");
                    return $this->render('message/_message.html.twig', [
                        'message' => $message,
                    ]);
                }

                error_log("✓ Redirecting to conversation page");
                return $this->redirectToRoute('app_messages_show', ['id' => $conversation->getId()]);
            } else {
                error_log("⚠ Empty message content, skipping");
            }
        }

        $messages = $messageRepo->findBy(['conversation' => $conversation], ['sent_at' => 'ASC']);

        // Find recipient for header
        $recipient = null;
        foreach ($conversation->getMembers() as $member) {
            if ($member->getUser() !== $user) {
                $recipient = $member->getUser();
                break;
            }
        }

        // No longer need Mercure cookie or URL
        $socketIoUrl = 'http://localhost:3001'; // In production, this would be your public URL

        return $this->render('message/show.html.twig', [
            'conversation' => $conversation,
            'messages' => $messages,
            'recipient' => $recipient,
            'socketIoUrl' => $socketIoUrl,
            'conversations' => $em->getRepository(Conversation::class)->findConversationsForUser($user->getId())
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['POST'])]
    public function edit(Message $message, Request $request, EntityManagerInterface $em): Response
    {
        if ($message->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You can only edit your own messages.');
        }

        $content = trim($request->request->get('content', ''));
        if ($content !== '') {
            $message->setContent($content);
            $message->setEditedAt(new \DateTime());
            $em->flush();

            // Notify via Redis (optional for now, but good practice)
        }

        if ($request->headers->has('HX-Request')) {
            return $this->render('message/_message.html.twig', ['message' => $message]);
        }

        return $this->redirectToRoute('app_messages_show', ['id' => $message->getConversation()->getId()]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Message $message, EntityManagerInterface $em, Request $request): Response
    {
        if ($message->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You can only delete your own messages.');
        }

        $message->setIsDeleted(true);
        $em->flush();

        if ($request->headers->has('HX-Request')) {
            return $this->render('message/_message.html.twig', ['message' => $message]);
        }

        return $this->redirectToRoute('app_messages_show', ['id' => $message->getConversation()->getId()]);
    }

    #[Route('/{id}/forward', name: 'forward_msg', methods: ['POST'])]
    public function forwardMessage(Message $message, Request $request, EntityManagerInterface $em, ConversationRepository $convRepo): Response
    {
        $targetConvId = $request->request->get('target_conversation_id');
        $targetConv = $convRepo->find($targetConvId);

        if (!$targetConv) {
            throw $this->createNotFoundException('Target conversation not found.');
        }

        // Check if user is member of target
        $isMember = false;
        foreach ($targetConv->getMembers() as $m) {
            if ($m->getUser() === $this->getUser()) {
                $isMember = true;
                break;
            }
        }

        if (!$isMember) throw $this->createAccessDeniedException();

        $newMsg = new Message();
        $newMsg->setConversation($targetConv);
        $newMsg->setUser($this->getUser());
        $newMsg->setContent($message->getContent());
        $newMsg->setSentAt(new \DateTime());
        $newMsg->setIsForwarded(true);
        $newMsg->setStatus('SENT');

        $em->persist($newMsg);
        $em->flush();

        return $this->redirectToRoute('app_messages_show', ['id' => $targetConv->getId()]);
    }

    #[Route('/group/create', name: 'group_create', methods: ['POST'])]
    public function createGroup(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
        $name = trim($request->request->get('name', 'Group Chat'));
        $userIds = $request->request->all('users');
        $currentUser = $this->getUser();

        $conversation = new Conversation();
        $conversation->setType('GROUP');
        $conversation->setName($name);
        $conversation->setCreatedAt(new \DateTime());
        $conversation->setUser($currentUser);

        // Add creator as MOD
        $creator = new ConversationMember();
        $creator->setUser($currentUser);
        $creator->setJoinedAt(new \DateTime());
        $creator->setRole('MOD');
        $conversation->addMember($creator);

        foreach ($userIds as $uid) {
            $u = $userRepo->find($uid);
            if ($u && $u !== $currentUser) {
                $member = new ConversationMember();
                $member->setUser($u);
                $member->setJoinedAt(new \DateTime());
                $member->setRole('MEMBER');
                $conversation->addMember($member);
            }
        }

        $em->persist($conversation);
        $em->flush();

        return $this->redirectToRoute('app_messages_show', ['id' => $conversation->getId()]);
    }

    #[Route('/{id}/group/update', name: 'group_update', methods: ['POST'])]
    public function updateGroup(Conversation $conversation, Request $request, EntityManagerInterface $em): Response
    {
        $this->ensureIsMod($conversation);

        $name = trim($request->request->get('name', ''));
        if ($name !== '') {
            $conversation->setName($name);
            $em->flush();
        }

        return $this->redirectToRoute('app_messages_show', ['id' => $conversation->getId()]);
    }

    #[Route('/{id}/group/add-member', name: 'group_add_member', methods: ['POST'])]
    public function addMember(Conversation $conversation, Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
        $this->ensureIsMod($conversation);

        $userId = $request->request->get('user_id');
        $user = $userRepo->find($userId);

        if (!$user) throw $this->createNotFoundException();

        // Check if already member
        foreach ($conversation->getMembers() as $m) {
            if ($m->getUser() === $user) return $this->redirectToRoute('app_messages_show', ['id' => $conversation->getId()]);
        }

        $member = new ConversationMember();
        $member->setUser($user);
        $member->setJoinedAt(new \DateTime());
        $member->setRole('MEMBER');
        $conversation->addMember($member);

        $em->persist($member);
        $em->flush();

        return $this->redirectToRoute('app_messages_show', ['id' => $conversation->getId()]);
    }

    #[Route('/{id}/group/remove-member/{userId}', name: 'group_remove_member', methods: ['POST'])]
    public function removeMember(Conversation $conversation, #[MapEntity(id: 'userId')] User $user, EntityManagerInterface $em): Response
    {
        $this->ensureIsMod($conversation);

        foreach ($conversation->getMembers() as $m) {
            if ($m->getUser() === $user) {
                $em->remove($m);
                break;
            }
        }

        $em->flush();

        return $this->redirectToRoute('app_messages_show', ['id' => $conversation->getId()]);
    }

    #[Route('/{id}/group/leave', name: 'group_leave', methods: ['POST'])]
    public function leaveGroup(Conversation $conversation, EntityManagerInterface $em): Response
    {
        $currentUser = $this->getUser();
        foreach ($conversation->getMembers() as $m) {
            if ($m->getUser() === $currentUser) {
                $em->remove($m);
                break;
            }
        }

        $em->flush();

        return $this->redirectToRoute('app_messages_index');
    }

    #[Route('/{id}/group/delete', name: 'group_delete', methods: ['POST'])]
    public function deleteGroup(Conversation $conversation, EntityManagerInterface $em): Response
    {
        $this->ensureIsMod($conversation);

        // Delete all members and messages (cascading might handle it but let's be explicit if needed)
        // Actually Conversation entity has cascade: ['remove'] on members
        $em->remove($conversation);
        $em->flush();

        return $this->redirectToRoute('app_messages_index');
    }

    private function ensureIsMod(Conversation $conversation): void
    {
        $isMod = false;
        foreach ($conversation->getMembers() as $m) {
            if ($m->getUser() === $this->getUser() && $m->getRole() === 'MOD') {
                $isMod = true;
                break;
            }
        }

        if (!$isMod && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Only moderators can perform this action.');
        }
    }

    #[Route('/api/contacts', name: 'api_contacts', methods: ['GET'])]
    public function getContacts(
        UserRepository $userRepo,
        StudentEnrollmentRepository $enrollmentRepo,
        TeacherAssignmentRepository $taRepo
    ): Response {
        $currentUser = $this->getUser();
        $allUsers = $userRepo->findAll();
        $eligible = [];

        foreach ($allUsers as $u) {
            if ($u === $currentUser) continue;
            if ($this->checkMessagingPermission($currentUser, $u, $enrollmentRepo, $taRepo)) {
                $eligible[] = [
                    'id' => $u->getId(),
                    'name' => $u->getFirstName() . ' ' . $u->getLastName(),
                    'role' => $u->getRoles()[0],
                    'avatar' => $u->getProfilePicture() ? '/uploads/profiles/' . $u->getProfilePicture() : null
                ];
            }
        }

        return $this->json($eligible);
    }

    private function checkMessagingPermission(
        User $me,
        User $other,
        StudentEnrollmentRepository $enrollmentRepo,
        TeacherAssignmentRepository $taRepo
    ): bool {
        // Admins can message anyone
        if ($this->isGranted('ROLE_ADMIN')) return true;
        if ($this->isUserRole($other, 'ROLE_ADMIN')) return true;

        $myRoles = $me->getRoles();
        $otherRoles = $other->getRoles();

        $isMeStudent = in_array('ROLE_STUDENT', $myRoles);
        $isMeTeacher = in_array('ROLE_TEACHER', $myRoles);
        
        $isOtherStudent = in_array('ROLE_STUDENT', $otherRoles);
        $isOtherTeacher = in_array('ROLE_TEACHER', $otherRoles);

        // Teacher <-> Teacher: Always allowed
        if ($isMeTeacher && $isOtherTeacher) return true;

        // Student <-> Student: Must be in the same class
        if ($isMeStudent && $isOtherStudent) {
            $myEnrollment = $enrollmentRepo->findOneBy(['user' => $me], ['id' => 'DESC']);
            $otherEnrollment = $enrollmentRepo->findOneBy(['user' => $other], ['id' => 'DESC']);
            
            return $myEnrollment && $otherEnrollment && $myEnrollment->getClasse() === $otherEnrollment->getClasse();
        }

        // Student <-> Teacher: Teacher must be assigned to student's class
        if ($isMeStudent && $isOtherTeacher) {
            $myEnrollment = $enrollmentRepo->findOneBy(['user' => $me], ['id' => 'DESC']);
            if (!$myEnrollment) return false;

            $assignment = $taRepo->findOneBy([
                'teacher' => $other,
                'classe' => $myEnrollment->getClasse()
            ]);
            return (bool)$assignment;
        }

        // Teacher <-> Student
        if ($isMeTeacher && $isOtherStudent) {
            $otherEnrollment = $enrollmentRepo->findOneBy(['user' => $other], ['id' => 'DESC']);
            if (!$otherEnrollment) return false;

            $assignment = $taRepo->findOneBy([
                'teacher' => $me,
                'classe' => $otherEnrollment->getClasse()
            ]);
            return (bool)$assignment;
        }

        return false;
    }

    private function isUserRole(User $user, string $role): bool
    {
        return in_array($role, $user->getRoles());
    }
}
