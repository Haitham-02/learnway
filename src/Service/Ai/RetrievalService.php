<?php

namespace App\Service\Ai;

use App\Entity\User;
use App\Entity\Chapter;
use App\Entity\Classe;
use App\Entity\Livestream;
use App\Entity\Announcement;
use App\Entity\Assignment;
use App\Entity\Subject;
use App\Entity\ForumPost;
use App\Entity\ConversationMember;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;

class RetrievalService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AuthorizationService $authService,
        private VectorSearchService $vectorSearch
    ) {}

    /**
     * Retrieve authorized context for a user query.
     */
    public function getAuthorizedContext(User $user, string $query): string
    {
        $context = "";

        // 1. Fetch Authorized Class IDs
        $classIds = $this->authService->getAuthorizedClassIds($user);
        
        if (empty($classIds)) {
            return "No authorized data found for this user.";
        }

        // 2. Fetch Class Details
        $classes = $this->entityManager->getRepository(Classe::class)->findBy(['id' => $classIds]);
        $classNames = array_map(fn($c) => $c->getName(), $classes);
        $context .= "AUTHORIZED CLASSES: " . implode(', ', $classNames) . "\n\n";

        // 3. Fetch Subjects and Lessons (Chapters)
        $chapters = $this->entityManager->getRepository(Chapter::class)->findBy(
            ['classe' => $classIds],
            ['sort_order' => 'ASC']
        );

        $context .= "LESSONS & SUBJECTS:\n";
        $foundSubjects = [];
        foreach ($chapters as $chapter) {
            $subj = $chapter->getSubject();
            if ($subj && !isset($foundSubjects[$subj->getId()])) {
                $foundSubjects[$subj->getId()] = $subj->getName();
            }
            $context .= "- Lesson: " . $chapter->getTitle() . " (Subject: " . ($subj?->getName() ?? 'N/A') . ", Class: " . $chapter->getClasse()?->getName() . ") - " . $chapter->getDescription() . "\n";
        }
        $context .= "Authorized Subjects: " . implode(', ', $foundSubjects) . "\n\n";

        // 4. Fetch Assignments
        $chapterIds = array_map(fn($c) => $c->getId(), $chapters);
        if (!empty($chapterIds)) {
            $assignments = $this->entityManager->getRepository(Assignment::class)->findBy(
                ['chapter' => $chapterIds],
                ['due_date' => 'ASC']
            );
            $context .= "ASSIGNMENTS:\n";
            foreach ($assignments as $asn) {
                $dueDate = $asn->getDue_date()?->format('Y-m-d H:i');
                $context .= "- " . $asn->getTitle() . " [Due: $dueDate, Type: " . $asn->getType() . "] for Lesson: " . $asn->getChapter()?->getTitle() . "\n";
            }
            $context .= "\n";
        }

        // 5. Fetch Forum Posts (Global or Class-specific, exclude REJECTED)
        $qb = $this->entityManager->getRepository(ForumPost::class)->createQueryBuilder('fp');
        $forumPosts = $qb->where($qb->expr()->orX(
                            $qb->expr()->in('fp.classe', ':classIds'),
                            $qb->expr()->isNull('fp.classe')
                         ))
                         ->andWhere("fp.status != 'REJECTED'")
                         ->setParameter('classIds', empty($classIds) ? [0] : $classIds)
                         ->orderBy('fp.created_at', 'DESC')
                         ->setMaxResults(8)
                         ->getQuery()
                         ->getResult();
        $context .= "FORUM POSTS:\n";
        foreach ($forumPosts as $post) {
            $date = $post->getCreated_at()?->format('M d');
            $context .= "- [{$post->getStatus()}] " . $post->getTitle() . " by " . ($post->getUser()?->getFirst_name() ?? 'Unknown') . " ($date): " . mb_substr(strip_tags($post->getContent()), 0, 120) . "\n";
            
            // Include comments for this post
            $comments = $post->getForumComments();
            if (count($comments) > 0) {
                $context .= "  Comments:\n";
                foreach ($comments as $comment) {
                    $cDate = $comment->getCreated_at()?->format('M d');
                    $context .= "    - " . ($comment->getUser()?->getFirst_name() ?? 'Unknown') . " ($cDate): " . mb_substr(strip_tags($comment->getContent()), 0, 100) . "\n";
                }
            }
        }
        $context .= "\n";

        // 6. Fetch Recent Messages
        $memberships = $this->entityManager->getRepository(ConversationMember::class)->findBy(['user' => $user]);
        $conversationIds = array_map(fn($m) => $m->getConversation()?->getId(), $memberships);
        
        if (!empty($conversationIds)) {
            $messages = $this->entityManager->getRepository(Message::class)->findBy(
                ['conversation' => $conversationIds],
                ['sent_at' => 'DESC'],
                5
            );
            $context .= "RECENT MESSAGES:\n";
            foreach ($messages as $msg) {
                $sender = $msg->getUser() === $user ? "You" : ($msg->getUser()?->getFirst_name() ?? "Someone");
                $context .= "- From $sender: " . $msg->getContent() . "\n";
            }
            $context .= "\n";
        }

        // 7. Fetch Recent Livestreams — sorted by createdAt (Doctrine camelCase mapping)
        $qbLs = $this->entityManager->createQueryBuilder();
        $livestreams = $qbLs->select('ls')
            ->from(Livestream::class, 'ls')
            ->where('ls.classe IN (:classIds)')
            ->setParameter('classIds', $classIds)
            ->orderBy('ls.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()->getResult();
        $context .= "RECENT LIVESTREAMS:\n";
        foreach ($livestreams as $ls) {
            $status = $ls->getStatus();
            $scheduled = $ls->getScheduledAt()?->format('Y-m-d H:i') ?? 'TBD';
            $started = $ls->getStartedAt()?->format('Y-m-d H:i');
            $context .= "- " . $ls->getTitle() . " [Status: $status, Scheduled: $scheduled" . ($started ? ", Started: $started" : "") . "] (Class: " . $ls->getClasse()?->getName() . ")\n";
        }
        $context .= "\n";

        // 8. Fetch Recent Announcements
        $announcements = $this->entityManager->getRepository(Announcement::class)->findBy(
            ['target_type' => 'class', 'target_id' => $classIds],
            ['publish_at' => 'DESC'],
            5
        );
        $context .= "ANNOUNCEMENTS:\n";
        foreach ($announcements as $ann) {
            $context .= "- " . $ann->getTitle() . ": " . $ann->getContent() . "\n";
        }
        $context .= "\n";

        // ROLE-SPECIFIC CONTEXTS
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            $context .= "--- SYSTEM ADMIN DATA ---\n";
            $totalUsers = $this->entityManager->getRepository(User::class)->count([]);
            $totalClasses = $this->entityManager->getRepository(Classe::class)->count([]);
            $context .= "Total Users: $totalUsers | Total Classes: $totalClasses\n";

            // Pending Forum Posts
            $pendingPosts = $this->entityManager->getRepository(ForumPost::class)->findBy(['status' => 'PENDING'], ['created_at' => 'DESC'], 5);
            if (count($pendingPosts) > 0) {
                $context .= "PENDING FORUM POSTS FOR MODERATION:\n";
                foreach ($pendingPosts as $p) {
                    $context .= "- " . $p->getTitle() . " by " . ($p->getUser()?->getFirst_name() ?? 'Unknown') . "\n";
                }
            }

            // Global Facial Analysis Pulse
            $qbFace = $this->entityManager->createQueryBuilder();
            $qbFace->select('f.emotion, COUNT(f.id) as count')
                ->from(\App\Entity\FacialAnalysis::class, 'f')
                ->groupBy('f.emotion')
                ->orderBy('count', 'DESC')
                ->setMaxResults(5);
            $emotions = $qbFace->getQuery()->getResult();
            if (count($emotions) > 0) {
                $context .= "PLATFORM-WIDE STUDENT MOOD/ENGAGEMENT:\n";
                foreach ($emotions as $em) {
                    $context .= "- Emotion: " . $em['emotion'] . " (Count: " . $em['count'] . ")\n";
                }
            }

            // Low Rated Forum Posts
            $qbReview = $this->entityManager->createQueryBuilder();
            $qbReview->select('r')
                ->from(\App\Entity\ForumReview::class, 'r')
                ->where('r.rating <= 2')
                ->orderBy('r.created_at', 'DESC')
                ->setMaxResults(3);
            $lowReviews = $qbReview->getQuery()->getResult();
            if (count($lowReviews) > 0) {
                $context .= "POORLY RATED FORUM POSTS (Needs Investigation):\n";
                foreach ($lowReviews as $r) {
                    $context .= "- Post ID: " . $r->getForumPost()?->getId() . " | Rating: " . $r->getRating() . " | Review: " . $r->getReview_text() . "\n";
                }
            }
            $context .= "\n";
        } elseif (in_array('ROLE_TEACHER', $roles)) {
            $context .= "--- TEACHER DATA ---\n";
            // Submissions needing grading for teacher's assignments
            $qbSub = $this->entityManager->createQueryBuilder();
            $qbSub->select('s')
                ->from(\App\Entity\Submission::class, 's')
                ->join('s.assignment', 'a')
                ->join('a.chapter', 'c')
                ->where('c.classe IN (:classIds)')
                ->andWhere('s.status = :status OR s.feedback IS NULL')
                ->setParameter('classIds', empty($classIds) ? [0] : $classIds)
                ->setParameter('status', 'submitted')
                ->setMaxResults(5);
            $pendingSubmissions = $qbSub->getQuery()->getResult();
            
            if (count($pendingSubmissions) > 0) {
                $context .= "SUBMISSIONS NEEDING GRADING:\n";
                foreach ($pendingSubmissions as $sub) {
                    $context .= "- " . $sub->getAssignment()?->getTitle() . " by " . ($sub->getStudent()?->getFirst_name() ?? 'Student') . " (Submitted: " . ($sub->getSubmitted_at()?->format('M d') ?? '') . ")\n";
                }
            }

            // Unanswered Livestream Q&A
            $qbQA = $this->entityManager->createQueryBuilder();
            $qbQA->select('qa')
                ->from(\App\Entity\LivestreamQA::class, 'qa')
                ->join('qa.livestream', 'ls')
                ->where('ls.classe IN (:classIds)')
                ->andWhere('qa.answer IS NULL')
                ->setParameter('classIds', empty($classIds) ? [0] : $classIds)
                ->orderBy('qa.createdAt', 'DESC')
                ->setMaxResults(5);
            $unansweredQA = $qbQA->getQuery()->getResult();
            if (count($unansweredQA) > 0) {
                $context .= "UNANSWERED LIVESTREAM QUESTIONS:\n";
                foreach ($unansweredQA as $qa) {
                    $context .= "- Stream: " . $qa->getLivestream()?->getTitle() . " | Question by " . ($qa->getStudent()?->getFirst_name() ?? 'Student') . ": " . $qa->getQuestion() . "\n";
                }
            }

            // Engagement Analytics for their classes
            $qbFace = $this->entityManager->createQueryBuilder();
            $qbFace->select('f.emotion, COUNT(f.id) as count')
                ->from(\App\Entity\FacialAnalysis::class, 'f')
                ->join('f.livestream', 'ls')
                ->where('ls.classe IN (:classIds)')
                ->setParameter('classIds', empty($classIds) ? [0] : $classIds)
                ->groupBy('f.emotion')
                ->orderBy('count', 'DESC')
                ->setMaxResults(5);
            $emotions = $qbFace->getQuery()->getResult();
            if (count($emotions) > 0) {
                $context .= "RECENT STUDENT ENGAGEMENT (From Livestreams):\n";
                foreach ($emotions as $em) {
                    $context .= "- Emotion: " . $em['emotion'] . " (Count: " . $em['count'] . ")\n";
                }
            }
            $context .= "\n";
        } else {
            $context .= "--- STUDENT DATA ---\n";
            // Graded submissions with feedback
            $qbSub = $this->entityManager->createQueryBuilder();
            $qbSub->select('s')
                ->from(\App\Entity\Submission::class, 's')
                ->where('s.student = :user')
                ->andWhere('s.feedback IS NOT NULL')
                ->setParameter('user', $user)
                ->orderBy('s.reviewed_at', 'DESC')
                ->setMaxResults(3);
            $gradedSubmissions = $qbSub->getQuery()->getResult();
            
            if (count($gradedSubmissions) > 0) {
                $context .= "RECENT GRADED ASSIGNMENTS:\n";
                foreach ($gradedSubmissions as $sub) {
                    $context .= "- " . $sub->getAssignment()?->getTitle() . ": " . strip_tags($sub->getFeedback()) . "\n";
                }
            }

            // Chapter Progress
            $qbProg = $this->entityManager->createQueryBuilder();
            $qbProg->select('cp')
                ->from(\App\Entity\ChapterProgress::class, 'cp')
                ->where('cp.user = :user')
                ->andWhere('cp.is_completed = false')
                ->setParameter('user', $user)
                ->setMaxResults(3);
            $inProgress = $qbProg->getQuery()->getResult();
            if (count($inProgress) > 0) {
                $context .= "LESSONS CURRENTLY IN PROGRESS:\n";
                foreach ($inProgress as $prog) {
                    $context .= "- " . $prog->getChapter()?->getTitle() . "\n";
                }
            }
            $context .= "\n";
        }

        // 9. Fetch relevant Vector Data
        $vectorResults = $this->vectorSearch->search($query, [
            'class_ids' => $classIds,
            'user_id' => $user->getId()
        ]);

        if (!empty($vectorResults)) {
            $context .= "SEARCH RESULTS FROM DOCUMENTS:\n";
            foreach ($vectorResults as $result) {
                $context .= "- Snippet from " . ($result['payload']['source'] ?? 'Document') . ": " . $result['payload']['content'] . "\n";
            }
        }

        return $context;
    }
}
