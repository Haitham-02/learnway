<?php

namespace App\Service\Ai;

use App\Entity\User;
use App\Entity\AiChat;
use App\Entity\AiMessage;
use Doctrine\ORM\EntityManagerInterface;
use Gemini\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;

class AiOrchestrationService
{
    private ?Client $gemini = null;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RetrievalService $retrievalService,
        private ParameterBagInterface $params,
        private LoggerInterface $logger
    ) {
        try {
            $geminiKey = $this->params->get('gemini_api_key');
            if ($geminiKey) {
                $this->gemini = \Gemini::client($geminiKey);
            }
        } catch (\Exception $e) {
            $this->logger->error('Failed to initialize Gemini client: ' . $e->getMessage());
        }
    }

    public function getResponse(User $user, AiChat $chat, string $userMessage): string
    {
        try {
            // 1. Retrieve authorized context
            $context = $this->retrievalService->getAuthorizedContext($user, $userMessage);

            // 2. Prepare System Prompt (Role-Based Persona)
            $roles = $user->getRoles();
            $persona = "Study Assistant";
            $focus = "helping the student understand lessons, track assignments, prepare for exams, and participate in discussions.";

            if (in_array('ROLE_ADMIN', $roles)) {
                $persona = "System Administrator Assistant";
                $focus = "providing a high-level overview of platform health, user engagement, recent announcements, and moderation activities.";
            } elseif (in_array('ROLE_TEACHER', $roles)) {
                $persona = "Teaching Assistant";
                $focus = "helping the teacher grade assignments, plan lessons, check student progress, answer class questions, and manage livestreams.";
            }

            $systemPrompt = "You are Learnway AI, a $persona built into the Learnway e-learning platform. 
            Your goal is to assist the user by $focus
            You have comprehensive access to the user's dashboard data, including:
            - Authorized Classes and Subjects
            - Lessons (Chapters) and their descriptions
            - Assignments (with due dates and types)
            - Recent Forum Posts and community discussions
            - Recent Private Messages and Conversations
            - Recent Livestreams and Platform Announcements
            - Content from uploaded PDFs and documents (via semantic search)
            - Available pages for navigation and redirection
            
            Strictly answer questions based ONLY on the provided context.
            If the information is not in the context, say you don't know.
            NEVER reveal information about other users or classes not in the context.
            
            REDIRECTION CAPABILITY:
            If the user asks how to navigate somewhere, where to find a feature, or requests to be taken to a specific page, you MUST include the exact tag [REDIRECT:/path/to/page] in your response. Only use the paths provided in the 'AVAILABLE PAGES FOR NAVIGATION/REDIRECTION' section of your context. Always provide a brief, friendly confirmation message alongside the tag. Do not invent paths.

            User Role: " . implode(', ', $roles) . "
            User Name: " . $user->getFirst_name() . " " . $user->getLast_name() . "\n\n" .
            "CONTEXT:\n" . $context;

            // 3. Call Gemini
            if (!$this->gemini) {
                return "Learnway AI: I'm currently offline (Check API Configuration).";
            }

            // Using generativeModel API with the 3.1 Flash Lite model for higher rate limits (500 RPD)
            $model = $this->gemini->generativeModel(model: 'models/gemini-3.1-flash-lite')
                        ->withSystemInstruction(\Gemini\Data\Content::parse($systemPrompt));



            // We don't use startChat for now to avoid complex history issues, 
            // instead we pass context and recent messages
            $result = $model->generateContent($userMessage);
            $assistantResponse = $result->text();

            // 4. Save Messages
            $userMsg = new AiMessage();
            $userMsg->setChat($chat);
            $userMsg->setRole('user');
            $userMsg->setContent($userMessage);
            
            $assistantMsg = new AiMessage();
            $assistantMsg->setChat($chat);
            $assistantMsg->setRole('assistant');
            $assistantMsg->setContent($assistantResponse);

            $this->entityManager->persist($userMsg);
            $this->entityManager->persist($assistantMsg);
            $this->entityManager->flush();

            return $assistantResponse;
        } catch (\Exception $e) {
            $this->logger->error('AI Orchestration Error: ' . $e->getMessage());
            return "Learnway AI: I encountered an internal error. Please try again later. (" . $e->getMessage() . ")";
        }
    }

    public function summarizeDocument(User $user, string $filename, string $content): string
    {
        try {
            if (!$this->gemini) {
                return "[ICON:check_circle_green] **$filename** has been indexed. (Summarization unavailable offline)";
            }

            $prompt = "You are Learnway AI. The user just uploaded a document named '$filename'. 
            Here is the beginning of the text from the document (it may be cut off if the document is very large):
            
            ---\n$content\n---
            
            Provide a very concise, easy-to-read summary of this document in 2-3 sentences. 
            Format it nicely using markdown. Do not ask follow up questions, just provide the summary.";

            $model = $this->gemini->generativeModel(model: 'models/gemini-3.1-flash-lite');
            $result = $model->generateContent($prompt);
            
            return "[ICON:check_circle_green] **$filename** has been indexed into your knowledge base!\n\n**Quick Summary:**\n" . $result->text();
        } catch (\Exception $e) {
            $this->logger->error('Summarization Error: ' . $e->getMessage());
            return "[ICON:cancel] **$filename** has been indexed. (Summarization failed: " . $e->getMessage() . ")";
        }
    }
}
