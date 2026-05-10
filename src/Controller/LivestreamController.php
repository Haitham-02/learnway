<?php

namespace App\Controller;

use App\Entity\Livestream;
use App\Entity\LivestreamParticipant;
use App\Entity\LivestreamQA;
use App\Entity\FacialAnalysis;
use App\Entity\LivestreamChat;
use App\Entity\Classe;
use App\Entity\User;
use App\Repository\LivestreamRepository;
use App\Repository\ClasseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Predis\Client as RedisClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/livestream', name: 'app_livestream_')]
#[IsGranted('ROLE_USER')]
class LivestreamController extends AbstractController
{
    private $redis;

    public function __construct(
        private EntityManagerInterface $em,
        private LivestreamRepository $livestreamRepo,
        private ClasseRepository $classeRepo,
        private \App\Repository\TeacherAssignmentRepository $taRepo,
        private \App\Repository\SubjectRepository $subjectRepo
    ) {
        // Connect to Redis for real-time events
        $redisHost = $_ENV['REDIS_HOST'] ?? 'localhost';
        $this->redis = new RedisClient(['host' => $redisHost]);
    }

    // ============= TEACHER ROUTES =============

    #[Route('/teacher/livestreams', name: 'teacher_list')]
    #[IsGranted('ROLE_TEACHER')]
    public function teacherList(): Response
    {
        $user = $this->getUser();
        $livestreams = $this->livestreamRepo->findTeacherLivestreams($user->getId());

        return $this->render('livestream/teacher_index.html.twig', [
            'livestreams' => $livestreams,
        ]);
    }

    #[Route('/teacher/livestreams/create', name: 'teacher_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function teacherCreate(Request $request): Response
    {
        $user = $this->getUser();
        $assignments = $this->taRepo->findBy(['teacher' => $user]);

        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $assignmentId = $request->request->get('assignment_id'); // We'll use the assignment ID to be safe
            $scheduledAt = $request->request->get('scheduled_at');
            $description = $request->request->get('description');

            $ta = $this->taRepo->find($assignmentId);
            if (!$ta || $ta->getTeacher() !== $user) {
                $this->addFlash('error', 'Invalid assignment selection.');
                return $this->redirectToRoute('app_livestream_teacher_create');
            }

            $livestream = new Livestream();
            $livestream->setTitle($title);
            $livestream->setClasse($ta->getClasse());
            $livestream->setSubject($ta->getSubject());
            $livestream->setTeacher($user);
            $livestream->setDescription($description);
            $livestream->setMeetingRoom($this->livestreamRepo->generateUniqueMeetingRoom());
            $livestream->setScheduledAt(new \DateTime($scheduledAt));
            $livestream->setStatus('SCHEDULED');

            $this->em->persist($livestream);
            $this->em->flush();

            $this->addFlash('success', 'Livestream created successfully for ' . $ta->getSubject()->getName() . ' (' . $ta->getClasse()->getName() . ')');
            return $this->redirectToRoute('app_livestream_teacher_list');
        }

        return $this->render('livestream/teacher_create.html.twig', [
            'assignments' => $assignments,
        ]);
    }

    #[Route('/teacher/livestreams/{id}/edit', name: 'teacher_edit')]
    #[IsGranted('ROLE_TEACHER')]
    public function teacherEdit(int $id, Request $request): Response
    {
        $livestream = $this->livestreamRepo->find($id);
        if (!$livestream || !$this->isTeacherOfLivestream($livestream, $this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $classes = $this->classeRepo->findAll();

        if ($request->isMethod('POST')) {
            $livestream->setTitle($request->request->get('title'));
            $livestream->setDescription($request->request->get('description'));
            $livestream->setClasse($this->classeRepo->find($request->request->get('class_id')));
            $livestream->setScheduledAt(new \DateTime($request->request->get('scheduled_at')));

            $this->em->flush();
            $this->addFlash('success', 'Livestream updated!');
            return $this->redirectToRoute('app_livestream_teacher_list');
        }

        return $this->render('livestream/teacher_edit.html.twig', [
            'livestream' => $livestream,
            'classes' => $classes,
        ]);
    }

    #[Route('/teacher/livestreams/{id}/start', name: 'teacher_start', methods: ['POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function teacherStart(int $id): Response
    {
        $livestream = $this->livestreamRepo->find($id);
        if (!$livestream || !$this->isTeacherOfLivestream($livestream, $this->getUser())) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $livestream->setStatus('LIVE');
        $livestream->setStartedAt(new \DateTime());
        $this->em->flush();

        return new JsonResponse(['status' => 'started']);
    }

    #[Route('/teacher/livestreams/{id}/end', name: 'teacher_end', methods: ['POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function teacherEnd(int $id): Response
    {
        $livestream = $this->livestreamRepo->find($id);
        if (!$livestream || !$this->isTeacherOfLivestream($livestream, $this->getUser())) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $livestream->setStatus('ENDED');
        $livestream->setEndedAt(new \DateTime());

        // For security measures, delete all facial analysis data once the session is closed
        // But first, save general info about students who need more focus
        $facialAnalyses = $this->em->getRepository(FacialAnalysis::class)->findBy(['livestream' => $livestream]);
        
        $studentScores = [];
        foreach ($facialAnalyses as $analysis) {
            $studentId = $analysis->getStudent()->getId();
            $studentName = $analysis->getStudent()->getFirst_name() . ' ' . $analysis->getStudent()->getLast_name();
            
            if (!isset($studentScores[$studentId])) {
                $studentScores[$studentId] = [
                    'name' => $studentName,
                    'totalScore' => 0,
                    'count' => 0,
                    'emotions' => []
                ];
            }
            
            $data = $analysis->getAdditionalData();
            $score = $data['averageScore'] ?? 0;
            $studentScores[$studentId]['totalScore'] += $score;
            $studentScores[$studentId]['count'] += 1;
            
            $emotion = $analysis->getEmotion();
            if (!isset($studentScores[$studentId]['emotions'][$emotion])) {
                $studentScores[$studentId]['emotions'][$emotion] = 0;
            }
            $studentScores[$studentId]['emotions'][$emotion]++;
            
            // Delete the raw data for privacy
            $this->em->remove($analysis);
        }
        
        $needsFocus = [];
        foreach ($studentScores as $studentId => $data) {
            $avgScore = $data['count'] > 0 ? round($data['totalScore'] / $data['count']) : 0;
            
            // Flag students with less than 50% average focus/engagement
            if ($avgScore < 50) {
                arsort($data['emotions']);
                $dominantEmotion = array_key_first($data['emotions']);
                
                $needsFocus[] = [
                    'studentId' => $studentId,
                    'name' => $data['name'],
                    'averageScore' => $avgScore,
                    'dominantEmotion' => $dominantEmotion
                ];
            }
        }
        
        $livestream->setEngagementSummary([
            'studentsNeedingFocus' => $needsFocus,
            'totalStudentsAnalyzed' => count($studentScores)
        ]);

        $this->em->flush();

        return new JsonResponse(['status' => 'ended']);
    }

    #[Route('/teacher/livestreams/{id}/delete', name: 'teacher_delete', methods: ['POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function teacherDelete(int $id): Response
    {
        $livestream = $this->livestreamRepo->find($id);
        if (!$livestream || !$this->isTeacherOfLivestream($livestream, $this->getUser())) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        // Only allow deletion if not started
        if ($livestream->getStatus() !== 'SCHEDULED') {
            $this->addFlash('error', 'Cannot delete a livestream that has started.');
            return $this->redirectToRoute('app_livestream_teacher_list');
        }

        $this->em->remove($livestream);
        $this->em->flush();

        $this->addFlash('success', 'Livestream deleted.');
        return $this->redirectToRoute('app_livestream_teacher_list');
    }

    // ============= STUDENT ROUTES =============

    #[Route('/student/livestreams', name: 'student_list')]
    #[IsGranted('ROLE_STUDENT')]
    public function studentList(): Response
    {
        $user = $this->getUser();
        $livestreams = $this->livestreamRepo->findStudentLivestreams($user->getId());

        return $this->render('livestream/student_index.html.twig', [
            'livestreams' => $livestreams,
        ]);
    }

    // ============= LIVE SESSION PAGE =============

    #[Route('/{id}', name: 'show')]
    public function show(int $id): Response
    {
        $livestream = $this->livestreamRepo->find($id);
        if (!$livestream) {
            throw $this->createNotFoundException('Livestream not found.');
        }

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Check if user can access this livestream
        $hasAccess = false;
        $hasAccess = $this->canAccessLivestream($livestream, $user);

        if (!$hasAccess) {
            throw $this->createAccessDeniedException();
        }

        // Record participant join
        $existingParticipant = $this->em->getRepository(LivestreamParticipant::class)
            ->findOneBy(['livestream' => $livestream, 'user' => $user]);

        if (!$existingParticipant) {
            $participant = new LivestreamParticipant();
            $participant->setLivestream($livestream);
            $participant->setUser($user);
            $participant->setRole($this->isTeacherOfLivestream($livestream, $user) ? 'TEACHER' : 'STUDENT');
            $this->em->persist($participant);
            $this->em->flush();
        }

        $questions = $this->em->getRepository(LivestreamQA::class)->findByLivestream($livestream->getId());
        $chats = $this->em->getRepository(LivestreamChat::class)->findByLivestream($livestream->getId());

        return $this->render('livestream/show.html.twig', [
            'livestream' => $livestream,
            'questions' => $questions,
            'chats' => $chats,
            'isTeacher' => $this->isTeacherOfLivestream($livestream, $user),
        ]);
    }

    // ============= API ENDPOINTS =============

    #[Route('/api/question/{id}/ask', name: 'api_ask_question', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function askQuestion(int $id, Request $request): Response
    {
        $livestream = $this->livestreamRepo->find($id);
        if (!$livestream) {
            return new JsonResponse(['error' => 'Livestream not found'], 404);
        }

        // Verify user has access to this livestream
        $user = $this->getUser();
        $hasAccess = $this->canAccessLivestream($livestream, $user);
        if (!$hasAccess) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $question = $request->request->get('question');

        if (!$question || trim($question) === '') {
            return new JsonResponse(['error' => 'Question is required'], 400);
        }

        $qa = new LivestreamQA();
        $qa->setLivestream($livestream);
        $qa->setStudent($user);
        $qa->setQuestion($question);

        $this->em->persist($qa);
        $this->em->flush();

        // Publish to Redis for real-time updates
        try {
            $eventData = json_encode([
                'room' => "livestream_{$livestream->getId()}_chat",
                'id' => $qa->getId(),
                'studentId' => $user->getId(),
                'studentName' => $user->getFirst_name() . ' ' . $user->getLast_name(),
                'question' => $qa->getQuestion(),
                'createdAt' => $qa->getCreatedAt()->format('Y-m-d H:i:s'),
            ]);
            $this->redis->publish('livestream-qa', $eventData);
        } catch (\Exception $e) {
            error_log("Redis publish failed for Q&A: " . $e->getMessage());
        }

        // If HTMX request, return partial template
        if ($request->headers->has('HX-Request')) {
            return $this->render('livestream/_qa_question.html.twig', [
                'question' => $qa,
                'isTeacher' => $this->isTeacherOfLivestream($livestream, $user),
            ]);
        }

        return new JsonResponse([
            'id' => $qa->getId(),
            'question' => $qa->getQuestion(),
            'student' => $user->getFirst_name() . ' ' . $user->getLast_name(),
            'created_at' => $qa->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/api/question/{questionId}/answer', name: 'api_answer_question', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function answerQuestion(int $questionId, Request $request): Response
    {
        $qa = $this->em->getRepository(LivestreamQA::class)->find($questionId);
        if (!$qa) {
            return new JsonResponse(['error' => 'Question not found'], 404);
        }

        // Verify user has access to this livestream
        $user = $this->getUser();
        $hasAccess = $this->canAccessLivestream($qa->getLivestream(), $user);
        if (!$hasAccess) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $answer = $request->request->get('answer');
        if (!$answer || trim($answer) === '') {
            return new JsonResponse(['error' => 'Answer is required'], 400);
        }

        $qa->setAnswer($answer);
        $qa->setAnsweredBy($user);
        $qa->setAnsweredAt(new \DateTime());

        $this->em->flush();

        // Publish to Redis for real-time updates
        try {
            $eventData = json_encode([
                'room' => "livestream_{$qa->getLivestream()->getId()}_chat",
                'questionId' => $qa->getId(),
                'answeredByName' => $user->getFirst_name() . ' ' . $user->getLast_name(),
                'answer' => $qa->getAnswer(),
            ]);
            $this->redis->publish('livestream-qa-answer', $eventData);
        } catch (\Exception $e) {
            error_log("Redis publish failed for Q&A answer: " . $e->getMessage());
        }

        // If HTMX request, return updated question template
        if ($request->headers->has('HX-Request')) {
            return $this->render('livestream/_qa_question.html.twig', [
                'question' => $qa,
                'isTeacher' => $this->isTeacherOfLivestream($qa->getLivestream(), $user),
            ]);
        }

        return new JsonResponse(['status' => 'answered']);
    }

    #[Route('/api/chat/{id}/send', name: 'api_send_chat', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function sendChat(int $id, Request $request): Response
    {
        $livestream = $this->livestreamRepo->find($id);
        if (!$livestream) {
            return new JsonResponse(['error' => 'Livestream not found'], 404);
        }

        // Verify user has access to this livestream
        $user = $this->getUser();
        $hasAccess = $this->canAccessLivestream($livestream, $user);
        if (!$hasAccess) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $message = $request->request->get('message');

        if (!$message || trim($message) === '') {
            return new JsonResponse(['error' => 'Message is required'], 400);
        }

        $chat = new LivestreamChat();
        $chat->setLivestream($livestream);
        $chat->setUser($user);
        $chat->setMessage($message);

        $this->em->persist($chat);
        $this->em->flush();

        // Publish to Redis for real-time updates
        try {
            $eventData = json_encode([
                'room' => "livestream_{$livestream->getId()}_chat",
                'id' => $chat->getId(),
                'userId' => $user->getId(),
                'userName' => $user->getFirst_name() . ' ' . $user->getLast_name(),
                'message' => $chat->getMessage(),
                'createdAt' => $chat->getCreatedAt()->format('H:i'),
            ]);
            $this->redis->publish('livestream-chat', $eventData);
        } catch (\Exception $e) {
            error_log("Redis publish failed for chat: " . $e->getMessage());
        }

        // If HTMX request, return partial template
        if ($request->headers->has('HX-Request')) {
            return $this->render('livestream/_chat_message.html.twig', [
                'chat' => $chat,
            ]);
        }

        return new JsonResponse([
            'id' => $chat->getId(),
            'message' => $chat->getMessage(),
            'user' => $user->getFirst_name() . ' ' . $user->getLast_name(),
            'created_at' => $chat->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/api/facial-data', name: 'api_facial_data', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function saveFacialData(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $livestreamId = $data['livestreamId'] ?? null;
        $emotion = $data['emotion'] ?? null;
        $confidence = $data['confidence'] ?? null;
        $averageScore = $data['averageScore'] ?? 0;

        if (!$livestreamId || !$emotion) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $livestream = $this->livestreamRepo->find($livestreamId);
        if (!$livestream) {
            return new JsonResponse(['error' => 'Livestream not found'], 404);
        }

        // Verify user has access to this livestream
        $user = $this->getUser();
        if (!$this->canAccessLivestream($livestream, $user)) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $facial = new FacialAnalysis();
        $facial->setLivestream($livestream);
        $facial->setStudent($user);
        $facial->setEmotion($emotion);
        $facial->setConfidence($confidence ?? 0);
        
        $additionalData = $data['additionalData'] ?? [];
        $additionalData['averageScore'] = $averageScore;
        $facial->setAdditionalData($additionalData);

        $this->em->persist($facial);
        $this->em->flush();

        // Broadcast to teacher via Redis/Socket.io
        try {
            $eventData = json_encode([
                'room' => "livestream_{$livestream->getId()}_teacher",
                'type' => 'facial_update',
                'studentId' => $user->getId(),
                'studentName' => $user->getFirst_name() . ' ' . $user->getLast_name(),
                'emotion' => $emotion,
                'score' => $averageScore,
                'confidence' => $confidence,
                'timestamp' => $facial->getCreatedAt()->format('H:i:s')
            ]);
            $this->redis->publish('livestream-ai-update', $eventData);
        } catch (\Exception $e) {
            // Silently fail redis broadcast
        }

        return new JsonResponse(['status' => 'saved', 'id' => $facial->getId()]);
    }

    #[Route('/api/emotion-stats/{id}', name: 'api_emotion_stats', methods: ['GET'])]
    #[IsGranted('ROLE_TEACHER')]
    public function getEmotionStats(int $id): JsonResponse
    {
        $livestream = $this->livestreamRepo->find($id);
        if (!$livestream) {
            return new JsonResponse(['error' => 'Livestream not found'], 404);
        }

        // Only allow teacher of this livestream to access emotion stats
        if (!$this->isTeacherOfLivestream($livestream, $this->getUser())) {
            return new JsonResponse(['error' => 'Unauthorized: Only the livestream teacher can access emotion stats'], 403);
        }

        $stats = $this->em->getRepository(FacialAnalysis::class)->getEmotionStats($id);

        return new JsonResponse($stats);
    }

    // ============= HELPER METHODS =============

    /**
     * Verify if a user has access to a livestream.
     * Teachers own the livestream.
     * Students must be enrolled in the livestream's class.
     */
    private function isTeacherOfLivestream(Livestream $livestream, ?User $user): bool
    {
        $teacher = $livestream->getTeacher();

        return $user !== null
            && $teacher !== null
            && (string) $teacher->getId() === (string) $user->getId();
    }

    private function canAccessLivestream(Livestream $livestream, ?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // 1. Teachers of this livestream always have access
        if ($this->isTeacherOfLivestream($livestream, $user)) {
            return true;
        }

        // 2. Admins always have access
        if ($this->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // 3. Students must be enrolled in the class assigned to the livestream
        $livestreamClass = $livestream->getClasse();
        
        // If no class is assigned, we default to allowing ROLE_USER (or you can restrict to ROLE_TEACHER)
        if (!$livestreamClass) {
            return $this->isGranted('ROLE_USER');
        }

        // Check enrollments for students
        $enrollments = $user->getStudentEnrollments();
        foreach ($enrollments as $enrollment) {
            $enrollmentClass = $enrollment->getClasse();
            if ($enrollmentClass && (string) $enrollmentClass->getId() === (string) $livestreamClass->getId()) {
                return true;
            }
        }

        // 4. Also allow other teachers to view/participate (optional, but often desired)
        if ($this->isGranted('ROLE_TEACHER')) {
            return true;
        }

        return false;
    }
}
