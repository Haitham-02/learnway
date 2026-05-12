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
use App\Entity\AcademicYear;
use App\Entity\Term;
use App\Entity\ForumComment;
use App\Entity\StudentEnrollment;
use App\Entity\TeacherAssignment;
use App\Entity\ChapterProgress;
use App\Entity\Submission;
use App\Entity\FacialAnalysis;
use App\Entity\ClassSchedule;
use App\Entity\TimeSlot;
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

        // 0. Fetch Global Academic Context
        $currentYear = $this->entityManager->getRepository(AcademicYear::class)->findOneBy(['is_current' => true]);
        $currentTerm = $this->entityManager->getRepository(Term::class)->findOneBy(['is_current' => true]);
        if ($currentYear) {
            $context .= "CURRENT ACADEMIC YEAR: " . $currentYear->getName() . "\n";
        }
        if ($currentTerm) {
            $context .= "CURRENT TERM: " . $currentTerm->getName() . "\n";
        }
        $context .= "TODAY'S DATE: " . (new \DateTime())->format('l, F j, Y') . "\n";
        $context .= "YOU ARE LOGGED IN AS: " . $user->getFirst_name() . " " . $user->getLast_name() . " (ID: " . $user->getId() . ")\n";
        $context .= "YOUR ROLE: " . implode(', ', $user->getRoles()) . "\n\n";

        // 1. Fetch Authorized Class IDs
        $classIds = $this->authService->getAuthorizedClassIds($user);
        
        if (empty($classIds)) {
            return "No authorized data found for this user.";
        }

        // 2. Fetch Class Details
        $classes = $this->entityManager->getRepository(Classe::class)->findBy(['id' => $classIds]);
        $classNames = array_map(fn($c) => $c->getName(), $classes);
        $context .= "AUTHORIZED CLASSES: " . implode(', ', $classNames) . "\n\n";

        // 2.5 Fetch Today's Schedule for the user's classes
        $dayOfWeek = strtoupper((new \DateTime())->format('l'));
        $qbSchedule = $this->entityManager->createQueryBuilder();
        $schedules = $qbSchedule->select('cs')
            ->from(ClassSchedule::class, 'cs')
            ->where('cs.classe IN (:classIds)')
            ->andWhere('cs.dayOfWeek = :day')
            ->setParameter('classIds', $classIds)
            ->setParameter('day', $dayOfWeek)
            ->join('cs.timeSlot', 'ts')
            ->orderBy('ts.startTime', 'ASC')
            ->getQuery()->getResult();
            
        if (count($schedules) > 0) {
            $context .= "TODAY'S SCHEDULE ({$dayOfWeek}):\n";
            foreach ($schedules as $cs) {
                $ts = $cs->getTimeSlot();
                $context .= "- " . ($cs->getSubject()?->getName() ?? 'Class') . " | " . $ts->getStartTime()->format('H:i') . " - " . $ts->getEndTime()->format('H:i') . " | Class: " . $cs->getClasse()?->getName() . "\n";
            }
            $context .= "\n";
        }

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
        $context .= "Authorized Subjects: " . implode(', ', $foundSubjects) . "\n";

        // Fetch Subject-Teacher assignments for these classes
        $qbTeachers = $this->entityManager->createQueryBuilder();
        $teacherAssignments = $qbTeachers->select('ta')
            ->from(TeacherAssignment::class, 'ta')
            ->where('ta.classe IN (:classIds)')
            ->setParameter('classIds', $classIds)
            ->getQuery()->getResult();
        
        if (count($teacherAssignments) > 0) {
            $context .= "TEACHERS ASSIGNED TO THESE CLASSES:\n";
            foreach ($teacherAssignments as $ta) {
                $subj = $ta->getSubject();
                if ($subj) {
                    $foundSubjects[$subj->getId()] = $subj->getName();
                    $context .= "- " . $subj->getName() . ": " . $ta->getTeacher()?->getFirst_name() . " " . $ta->getTeacher()?->getLast_name() . " (" . $ta->getTeacher()?->getEmail() . ")\n";
                }
            }
        }
        $context .= "\n";

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
        $context .= "COMMUNITY FORUM POSTS:\n";
        foreach ($forumPosts as $post) {
            $date = $post->getCreated_at()?->format('M d');
            $authorName = $post->getUser()?->getFirst_name() . " " . $post->getUser()?->getLast_name();
            $authorId = $post->getUser()?->getId();
            $isYours = ($authorId === $user->getId());
            
            $context .= "- " . ($isYours ? "[YOUR POST] " : "[By $authorName, ID: $authorId] ") . "Title: " . $post->getTitle() . " ($date, Status: {$post->getStatus()}): " . mb_substr(strip_tags($post->getContent()), 0, 120) . "\n";
            
            // Include comments for this post
            $comments = $post->getForumComments();
            if (count($comments) > 0) {
                $context .= "  Comments:\n";
                foreach ($comments as $comment) {
                    $cDate = $comment->getCreated_at()?->format('M d');
                    $cAuthorName = $comment->getUser()?->getFirst_name() . " " . $comment->getUser()?->getLast_name();
                    $cAuthorId = $comment->getUser()?->getId();
                    $cIsYours = ($cAuthorId === $user->getId());
                    $context .= "    - " . ($cIsYours ? "[Your Comment] " : "[$cAuthorName, ID: $cAuthorId] ") . "($cDate): " . mb_substr(strip_tags($comment->getContent()), 0, 100) . "\n";
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
            
            // Available Admin Pages for AI Redirection
            $context .= "AVAILABLE PAGES FOR NAVIGATION/REDIRECTION:\n";
            $context .= "- Dashboard Overview: /dashboard\n";
            $context .= "- Community Forum: /forum\n";
            $context .= "- Messages & Inbox: /messages\n";
            $context .= "- Personal Schedule: /schedule\n";
            $context .= "- Manage Users (Students/Teachers): /admin/users\n";
            $context .= "- Manage Forum Posts: /admin/forum-posts\n";
            $context .= "- Manage Forum Comments: /admin/forum-comments\n";
            $context .= "- Setup Wizard / New Academic Year: /admin/setup/new-year\n";
            $context .= "- Manage Academic Years: /admin/academic-years\n";
            $context .= "- Manage Terms: /admin/terms\n";
            $context .= "- Manage Classes: /admin/classes\n";
            $context .= "- Academic Scheduling / Timetables: /admin/schedule\n";
            $context .= "- Manage Subjects: /admin/subjects\n";
            $context .= "- Manage Chapters / Lessons: /admin/chapters\n";
            $context .= "- Manage Announcements: /admin/announcements\n";
            $context .= "- Manage Student Enrollments: /admin/student-enrollments\n";
            $context .= "- Assign Teacher to Class: /admin/teacher-assignments\n\n";

            // Enhanced counts breakdown for Admin
            $qbUsers = $this->entityManager->createQueryBuilder();
            $userCounts = $qbUsers->select('r.name as roleName, COUNT(u.id) as count')
                ->from(User::class, 'u')
                ->join('u.role', 'r')
                ->groupBy('r.name')
                ->getQuery()->getResult();
            
            $userStats = [];
            foreach ($userCounts as $uc) {
                $userStats[] = $uc['roleName'] . "s: " . $uc['count'];
            }
            
            $totalUsers = $this->entityManager->getRepository(User::class)->count([]);
            $totalClasses = $this->entityManager->getRepository(Classe::class)->count([]);
            $totalSubjects = $this->entityManager->getRepository(Subject::class)->count([]);
            $totalEnrollments = $this->entityManager->getRepository(StudentEnrollment::class)->count([]);
            $totalForumPosts = $this->entityManager->getRepository(ForumPost::class)->count([]);
            $totalForumComments = $this->entityManager->getRepository(ForumComment::class)->count([]);

            $context .= "PLATFORM STATISTICS:\n";
            $context .= "- TOTAL USERS ON PLATFORM: $totalUsers\n";
            foreach ($userCounts as $uc) {
                $context .= "- TOTAL " . $uc['roleName'] . "s: " . $uc['count'] . "\n";
            }
            $context .= "- Total Classes: $totalClasses | Total Subjects: $totalSubjects\n";
            $context .= "- Active Student Enrollments: $totalEnrollments\n";
            $context .= "- Forum Activity: $totalForumPosts Posts | $totalForumComments Comments\n";

            // Top forum contributors
            $qbTop = $this->entityManager->createQueryBuilder();
            $topPosters = $qbTop->select('u.first_name, u.last_name, COUNT(fp.id) as postCount')
                ->from(ForumPost::class, 'fp')
                ->join('fp.user', 'u')
                ->groupBy('u.id')
                ->orderBy('postCount', 'DESC')
                ->setMaxResults(3)
                ->getQuery()->getResult();
            
            if (count($topPosters) > 0) {
                $context .= "- Top Forum Contributors: ";
                foreach ($topPosters as $tp) {
                    $context .= $tp['first_name'] . " (" . $tp['postCount'] . " posts), ";
                }
                $context = rtrim($context, ", ") . "\n";
            }

            // Inactive Users (last 7 days)
            $oneWeekAgo = (new \DateTime())->modify('-7 days');
            $inactiveCount = $this->entityManager->getRepository(User::class)->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->where('u.last_login_at < :date OR u.last_login_at IS NULL')
                ->setParameter('date', $oneWeekAgo)
                ->getQuery()->getSingleScalarResult();
            $context .= "- Engagement: $inactiveCount users have not logged in for 7+ days.\n\n";

            // Recently Registered Users
            $recentUsers = $this->entityManager->getRepository(User::class)->findBy([], ['created_at' => 'DESC'], 5);
            $context .= "RECENTLY REGISTERED USERS:\n";
            foreach ($recentUsers as $ru) {
                $context .= "- " . $ru->getFirst_name() . " " . $ru->getLast_name() . " (" . $ru->getEmail() . ") - Role: " . ($ru->getRole()?->getName() ?? 'N/A') . "\n";
            }
            $context .= "\n";

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
            
            // Available Teacher Pages for AI Redirection
            $context .= "AVAILABLE PAGES FOR NAVIGATION/REDIRECTION:\n";
            $context .= "- Dashboard Overview: /dashboard\n";
            $context .= "- Community Forum: /forum\n";
            $context .= "- Messages & Inbox: /messages\n";
            $context .= "- View Schedule & Upcoming Classes: /schedule\n\n";

            // Full weekly schedule for teachers (to allow change requests)
            $teacherSchedule = $this->entityManager->getRepository(ClassSchedule::class)->findBy(['teacher' => $user]);
            if (count($teacherSchedule) > 0) {
                $context .= "YOUR FULL WEEKLY TEACHING SCHEDULE:\n";
                foreach ($teacherSchedule as $cs) {
                    $context .= "- Schedule ID: " . $cs->getId() . " | Day: " . $cs->getDayOfWeek() . " | Slot: " . $cs->getTimeSlot()->getRange() . " | Subject: " . $cs->getSubject()->getName() . " | Class: " . $cs->getClasse()->getName() . "\n";
                }
                $context .= "\n";
            }

            // Available time slots (for proposing changes)
            $allSlots = $this->entityManager->getRepository(TimeSlot::class)->findAll();
            $context .= "AVAILABLE TIME SLOTS FOR PROPOSING CHANGES:\n";
            foreach ($allSlots as $ts) {
                $context .= "- Slot ID: " . $ts->getId() . " | Time: " . $ts->getRange() . " (" . $ts->getType() . ")\n";
            }
            $context .= "\n";

            // Recent Submissions activity for teachers
            $qbSubRecent = $this->entityManager->createQueryBuilder();
            $recentSubmissions = $qbSubRecent->select('s')
                ->from(Submission::class, 's')
                ->join('s.assignment', 'a')
                ->join('a.chapter', 'c')
                ->where('c.classe IN (:classIds)')
                ->setParameter('classIds', $classIds)
                ->orderBy('s.submitted_at', 'DESC') // Using snake_case for DQL
                ->setMaxResults(3)
                ->getQuery()->getResult();
            if (count($recentSubmissions) > 0) {
                $context .= "RECENT SUBMISSIONS ACTIVITY:\n";
                foreach ($recentSubmissions as $rs) {
                    $context .= "- " . ($rs->getStudent()?->getFirst_name() ?? 'Student') . " submitted " . $rs->getAssignment()?->getTitle() . " [Status: " . $rs->getStatus() . "]\n";
                }
                $context .= "\n";
            }

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

            // Available Student Pages for AI Redirection
            $context .= "AVAILABLE PAGES FOR NAVIGATION/REDIRECTION:\n";
            $context .= "- Dashboard Overview: /dashboard\n";
            $context .= "- Community Forum: /forum\n";
            $context .= "- Messages & Inbox: /messages\n";
            $context .= "- View Class Schedule: /schedule\n";
            foreach ($foundSubjects as $id => $name) {
                $context .= "- Subject $name: /student/subject/$id\n";
            }
            $context .= "\n";

            // Student Activity Stats
            $qbSubStats = $this->entityManager->createQueryBuilder();
            $subCount = $qbSubStats->select('COUNT(s.id)')
                ->from(Submission::class, 's')
                ->where('s.student = :user')
                ->setParameter('user', $user)
                ->getQuery()->getSingleScalarResult();
            $context .= "YOUR ACTIVITY: You have submitted $subCount assignments so far.\n";

            // Forum Activity for this specific user
            $qbMyForum = $this->entityManager->createQueryBuilder();
            $myPosts = $qbMyForum->select('fp')
                ->from(ForumPost::class, 'fp')
                ->where('fp.user = :user')
                ->setParameter('user', $user)
                ->orderBy('fp.created_at', 'DESC')
                ->setMaxResults(3)
                ->getQuery()->getResult();
            
            if (count($myPosts) > 0) {
                $context .= "YOUR FORUM POSTS:\n";
                foreach ($myPosts as $mp) {
                    $context .= "- " . $mp->getTitle() . " [Status: " . $mp->getStatus() . "]\n";
                }
            }

            // Subject progress completion
            $qbProgStats = $this->entityManager->createQueryBuilder();
            $progressStats = $qbProgStats->select('s.name as subjectName, COUNT(cp.id) as completedCount')
                ->from(ChapterProgress::class, 'cp')
                ->join('cp.chapter', 'c')
                ->join('c.subject', 's')
                ->where('cp.user = :user')
                ->andWhere('cp.completed_at IS NOT NULL')
                ->groupBy('s.id')
                ->setParameter('user', $user)
                ->getQuery()->getResult();
            if (count($progressStats) > 0) {
                $context .= "SUBJECT COMPLETION:\n";
                foreach ($progressStats as $ps) {
                    $context .= "- " . $ps['subjectName'] . ": " . $ps['completedCount'] . " lessons completed\n";
                }
            }
            $context .= "\n";

            // Missing Assignments (Not yet submitted)
            $qbMissing = $this->entityManager->createQueryBuilder();
            $missingAsn = $qbMissing->select('a')
                ->from(\App\Entity\Assignment::class, 'a')
                ->join('a.chapter', 'c')
                ->leftJoin(\App\Entity\Submission::class, 's', 'WITH', 's.assignment = a AND s.student = :user')
                ->where('c.classe IN (:classIds)')
                ->andWhere('s.id IS NULL')
                ->andWhere('a.due_date > :now')
                ->setParameter('user', $user)
                ->setParameter('classIds', $classIds)
                ->setParameter('now', new \DateTime())
                ->setMaxResults(5)
                ->getQuery()->getResult();
            
            if (count($missingAsn) > 0) {
                $context .= "UPCOMING ASSIGNMENTS (NOT YET SUBMITTED):\n";
                foreach ($missingAsn as $ma) {
                    $context .= "- " . $ma->getTitle() . " [Due: " . $ma->getDue_date()?->format('M d H:i') . "]\n";
                }
            }

            // Graded submissions with feedback
            $qbSub = $this->entityManager->createQueryBuilder();
            $qbSub->select('s')
                ->from(\App\Entity\Submission::class, 's')
                ->where('s.student = :user')
                ->andWhere('s.feedback IS NOT NULL')
                ->setParameter('user', $user)
                ->orderBy('s.reviewed_at', 'DESC') // Using snake_case for DQL
                ->setMaxResults(3);
            $gradedSubmissions = $qbSub->getQuery()->getResult();
            
            if (count($gradedSubmissions) > 0) {
                $context .= "RECENT GRADED ASSIGNMENTS:\n";
                foreach ($gradedSubmissions as $sub) {
                    $context .= "- " . $sub->getAssignment()?->getTitle() . ": " . strip_tags($sub->getFeedback()) . " [Status: " . $sub->getStatus() . "]\n";
                }
            }

            // Chapter Progress
            $qbProg = $this->entityManager->createQueryBuilder();
            $qbProg->select('cp')
                ->from(\App\Entity\ChapterProgress::class, 'cp')
                ->where('cp.user = :user')
                ->andWhere('cp.completed_at IS NULL')
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
