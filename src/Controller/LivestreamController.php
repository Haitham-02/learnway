<?php

namespace App\Controller;

use App\Entity\Livestream;
use App\Entity\LivestreamParticipant;
use App\Entity\LivestreamQA;
use App\Entity\FacialAnalysis;
use App\Entity\LivestreamChat;
use App\Entity\Classe;
use App\Repository\LivestreamRepository;
use App\Repository\ClasseRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function __construct(
        private EntityManagerInterface $em,
        private LivestreamRepository $livestreamRepo,
        private ClasseRepository $classeRepo
    ) {}

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

    #[Route('/teacher/livestreams/create', name: 'teacher_create')]
    #[IsGranted('ROLE_TEACHER')]
    public function teacherCreate(Request $request): Response
    {
        $classes = $this->classeRepo->findAll();

        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $classId = $request->request->get('class_id');
            $scheduledAt = $request->request->get('scheduled_at');
            $description = $request->request->get('description');

            $classe = $this->classeRepo->find($classId);
            if (!$classe) {
                $this->addFlash('error', 'Class not found.');
                return $this->redirectToRoute('app_livestream_teacher_list');
            }

            $livestream = new Livestream();
            $livestream->setTitle($title);
            $livestream->setClasse($classe);
            $livestream->setTeacher($this->getUser());
            $livestream->setDescription($description);
            $livestream->setMeetingRoom($this->livestreamRepo->generateUniqueMeetingRoom());
            $livestream->setScheduledAt(new \DateTime($scheduledAt));
            $livestream->setStatus('SCHEDULED');

            $this->em->persist($livestream);
            $this->em->flush();

            $this->addFlash('success', 'Livestream created successfully!');
            return $this->redirectToRoute('app_livestream_teacher_list');
        }

        return $this->render('livestream/teacher_create.html.twig', [
            'classes' => $classes,
        ]);
    }

    #[Route('/teacher/livestreams/{id}/edit', name: 'teacher_edit')]
    #[IsGranted('ROLE_TEACHER')]
    public function teacherEdit(int $id, Request $request): Response
    {
        $livestream = $this->livestreamRepo->find($id);
        if (!$livestream || $livestream->getTeacher() !== $this->getUser()) {
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
        if (!$livestream || $livestream->getTeacher() !== $this->getUser()) {
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
        if (!$livestream || $livestream->getTeacher() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $livestream->setStatus('ENDED');
        $livestream->setEndedAt(new \DateTime());
        $this->em->flush();

        return new JsonResponse(['status' => 'ended']);
    }

    #[Route('/teacher/livestreams/{id}/delete', name: 'teacher_delete', methods: ['POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function teacherDelete(int $id): Response
    {
        $livestream = $this->livestreamRepo->find($id);
        if (!$livestream || $livestream->getTeacher() !== $this->getUser()) {
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
        if ($livestream->getTeacher() === $user) {
            $hasAccess = true;
        } else {
            // Check if student is in the class
            $enrollments = $user->getStudentEnrollments();
            foreach ($enrollments as $enrollment) {
                if ($enrollment->getClasse() === $livestream->getClasse()) {
                    $hasAccess = true;
                    break;
                }
            }
        }

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
            $participant->setRole($livestream->getTeacher() === $user ? 'TEACHER' : 'STUDENT');
            $this->em->persist($participant);
            $this->em->flush();
        }

        $questions = $this->em->getRepository(LivestreamQA::class)->findByLivestream($livestream->getId());
        $chats = $this->em->getRepository(LivestreamChat::class)->findByLivestream($livestream->getId());

        return $this->render('livestream/show.html.twig', [
            'livestream' => $livestream,
            'questions' => $questions,
            'chats' => $chats,
            'isTeacher' => $livestream->getTeacher() === $user,
        ]);
    }

    // ============= API ENDPOINTS =============

    #[Route('/api/question/{id}/ask', name: 'api_ask_question', methods: ['POST'])]
    public function askQuestion(int $id, Request $request): JsonResponse
    {
        $livestream = $this->livestreamRepo->find($id);
        if (!$livestream) {
            return new JsonResponse(['error' => 'Livestream not found'], 404);
        }

        $user = $this->getUser();
        $question = $request->request->get('question');

        if (!$question) {
            return new JsonResponse(['error' => 'Question is required'], 400);
        }

        $qa = new LivestreamQA();
        $qa->setLivestream($livestream);
        $qa->setStudent($user);
        $qa->setQuestion($question);

        $this->em->persist($qa);
        $this->em->flush();

        return new JsonResponse([
            'id' => $qa->getId(),
            'question' => $qa->getQuestion(),
            'student' => $user->getFirst_name() . ' ' . $user->getLast_name(),
            'created_at' => $qa->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/api/question/{questionId}/answer', name: 'api_answer_question', methods: ['POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function answerQuestion(int $questionId, Request $request): JsonResponse
    {
        $qa = $this->em->getRepository(LivestreamQA::class)->find($questionId);
        if (!$qa) {
            return new JsonResponse(['error' => 'Question not found'], 404);
        }

        // Check if user is the teacher
        if ($qa->getLivestream()->getTeacher() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $answer = $request->request->get('answer');
        if (!$answer) {
            return new JsonResponse(['error' => 'Answer is required'], 400);
        }

        $qa->setAnswer($answer);
        $qa->setAnsweredBy($this->getUser());
        $qa->setAnsweredAt(new \DateTime());

        $this->em->flush();

        return new JsonResponse(['status' => 'answered']);
    }

    #[Route('/api/chat/{id}/send', name: 'api_send_chat', methods: ['POST'])]
    public function sendChat(int $id, Request $request): JsonResponse
    {
        $livestream = $this->livestreamRepo->find($id);
        if (!$livestream) {
            return new JsonResponse(['error' => 'Livestream not found'], 404);
        }

        $user = $this->getUser();
        $message = $request->request->get('message');

        if (!$message) {
            return new JsonResponse(['error' => 'Message is required'], 400);
        }

        $chat = new LivestreamChat();
        $chat->setLivestream($livestream);
        $chat->setUser($user);
        $chat->setMessage($message);

        $this->em->persist($chat);
        $this->em->flush();

        return new JsonResponse([
            'id' => $chat->getId(),
            'message' => $chat->getMessage(),
            'user' => $user->getFirst_name() . ' ' . $user->getLast_name(),
            'created_at' => $chat->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/api/facial-data', name: 'api_facial_data', methods: ['POST'])]
    public function saveFacialData(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $livestreamId = $data['livestreamId'] ?? null;
        $emotion = $data['emotion'] ?? null;
        $confidence = $data['confidence'] ?? null;

        if (!$livestreamId || !$emotion || !$confidence) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $livestream = $this->livestreamRepo->find($livestreamId);
        if (!$livestream) {
            return new JsonResponse(['error' => 'Livestream not found'], 404);
        }

        $facial = new FacialAnalysis();
        $facial->setLivestream($livestream);
        $facial->setStudent($this->getUser());
        $facial->setEmotion($emotion);
        $facial->setConfidence($confidence);
        $facial->setAdditionalData($data['additionalData'] ?? null);

        $this->em->persist($facial);
        $this->em->flush();

        return new JsonResponse(['status' => 'saved']);
    }

    #[Route('/api/emotion-stats/{id}', name: 'api_emotion_stats', methods: ['GET'])]
    public function getEmotionStats(int $id): JsonResponse
    {
        $livestream = $this->livestreamRepo->find($id);
        if (!$livestream) {
            return new JsonResponse(['error' => 'Livestream not found'], 404);
        }

        $stats = $this->em->getRepository(FacialAnalysis::class)->getEmotionStats($id);

        return new JsonResponse($stats);
    }
}
