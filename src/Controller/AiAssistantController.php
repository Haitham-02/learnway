<?php

namespace App\Controller;

use App\Entity\AiChat;
use App\Entity\User;
use App\Service\Ai\AiOrchestrationService;
use App\Service\Ai\FileProcessingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/ai')]
#[IsGranted('ROLE_USER')]
class AiAssistantController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AiOrchestrationService $orchestrationService,
        private FileProcessingService $fileProcessingService
    ) {}

    #[Route('/chat', name: 'api_ai_chat', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? '';
        $chatId = $data['chatId'] ?? null;

        /** @var User $user */
        $user = $this->getUser();

        if (empty($userMessage)) {
            return new JsonResponse(['error' => 'Message is required'], 400);
        }

        // Find or create chat
        if ($chatId) {
            $chat = $this->entityManager->getRepository(AiChat::class)->find($chatId);
            if (!$chat || $chat->getUser() !== $user) {
                return new JsonResponse(['error' => 'Chat not found'], 404);
            }
        } else {
            $chat = new AiChat();
            $chat->setUser($user);
            $chat->setTitle(mb_substr($userMessage, 0, 30) . '...');
            $this->entityManager->persist($chat);
            $this->entityManager->flush();
        }

        $response = $this->orchestrationService->getResponse($user, $chat, $userMessage);

        return new JsonResponse([
            'chatId' => $chat->getId(),
            'response' => $response
        ]);
    }

    #[Route('/upload', name: 'api_ai_upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $file = $request->files->get('file');
        if (!$file) {
            return new JsonResponse(['error' => 'No file uploaded'], 400);
        }

        /** @var User $user */
        $user = $this->getUser();

        $tempPath = $file->getRealPath();

        $originalName = $file->getClientOriginalName();
        $extractedText = $this->fileProcessingService->processFile($tempPath, $originalName, 'user_upload', $user->getId(), [
            'user_id' => $user->getId(),
            'original_name' => $originalName
        ]);

        $summary = "✅ File indexed successfully.";
        if (!empty($extractedText)) {
            $summary = $this->orchestrationService->summarizeDocument($user, $originalName, $extractedText);
        }

        return new JsonResponse([
            'status' => true,
            'summary' => $summary
        ]);
    }
}
