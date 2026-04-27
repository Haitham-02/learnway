<?php

namespace App\Controller\Admin;

use App\Entity\Chapter;
use App\Repository\AssignmentRepository;
use App\Repository\ChapterItemRepository;
use App\Repository\ChapterProgressRepository;
use App\Repository\ChapterRepository;
use App\Repository\ClasseRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminChapterController extends AbstractController
{
    #[Route('/chapters', name: 'chapters_index')]
    public function chaptersIndex(ChapterRepository $chapterRepository): Response
    {
        return $this->render('admin/chapters/index.html.twig', [
            'chapters' => $chapterRepository->findForAdminIndex(),
        ]);
    }

    #[Route('/chapters/new', name: 'chapters_new', methods: ['GET', 'POST'])]
    public function chaptersNew(
        Request $request,
        ClasseRepository $classeRepository,
        SubjectRepository $subjectRepository,
        EntityManagerInterface $em,
    ): Response {
        $classes = $classeRepository->findForSelector();
        $subjects = $subjectRepository->findForSelector();
        $errors = [];

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('chapter_create', (string) $request->request->get('_token'))) {
                $errors[] = 'Your session expired. Please try submitting the form again.';
            }

            $formData = $this->parseChapterFormData($request, $classeRepository, $subjectRepository);
            $errors = array_merge($errors, $formData['errors']);

            if (empty($errors)) {
                $chapter = new Chapter();
                $this->applyChapterFormData($chapter, $formData);
                $chapter->setCreatedAt(new \DateTime());
                $chapter->setUpdatedAt(new \DateTime());

                $em->persist($chapter);
                $em->flush();

                $this->addFlash('success', "Chapter «{$chapter->getTitle()}» created successfully.");
                $returnTo = trim((string) $request->request->get('return_to', $request->query->get('return_to', '')));
                if (str_starts_with($returnTo, '/admin/')) {
                    return $this->redirect($this->appendQueryParams($returnTo, [
                        'class_id' => $chapter->getClasse()?->getId(),
                        'subject_id' => $chapter->getSubject()?->getId(),
                    ]));
                }

                return $this->redirectToRoute('admin_chapters_index');
            }
        }

        return $this->render('admin/chapters/new.html.twig', [
            'classes' => $classes,
            'subjects' => $subjects,
            'errors' => $errors,
        ]);
    }

    #[Route('/chapters/{id}/edit', name: 'chapters_edit', methods: ['GET', 'POST'])]
    public function chaptersEdit(
        int $id,
        Request $request,
        ChapterRepository $chapterRepository,
        ClasseRepository $classeRepository,
        SubjectRepository $subjectRepository,
        EntityManagerInterface $em,
    ): Response {
        $chapter = $chapterRepository->find($id);
        if (!$chapter) {
            throw $this->createNotFoundException("Chapter #{$id} not found.");
        }

        $classes = $classeRepository->findForSelector();
        $subjects = $subjectRepository->findForSelector();
        $errors = [];

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('chapter_edit_' . $chapter->getId(), (string) $request->request->get('_token'))) {
                $errors[] = 'Your session expired. Please try submitting the form again.';
            }

            $formData = $this->parseChapterFormData($request, $classeRepository, $subjectRepository);
            $errors = array_merge($errors, $formData['errors']);

            if (empty($errors)) {
                $this->applyChapterFormData($chapter, $formData);
                $chapter->setUpdatedAt(new \DateTime());

                $em->flush();

                $this->addFlash('success', "Chapter «{$chapter->getTitle()}» updated successfully.");
                return $this->redirectToRoute('admin_chapters_index');
            }
        }

        return $this->render('admin/chapters/edit.html.twig', [
            'chapter' => $chapter,
            'classes' => $classes,
            'subjects' => $subjects,
            'errors' => $errors,
        ]);
    }

    #[Route('/chapters/{id}/delete', name: 'chapters_delete', methods: ['POST'])]
    public function chaptersDelete(
        int $id,
        Request $request,
        ChapterRepository $chapterRepository,
        AssignmentRepository $assignmentRepository,
        ChapterItemRepository $chapterItemRepository,
        ChapterProgressRepository $chapterProgressRepository,
        EntityManagerInterface $em,
    ): Response {
        $chapter = $chapterRepository->find($id);
        if (!$chapter) {
            throw $this->createNotFoundException("Chapter #{$id} not found.");
        }

        if ($this->isCsrfTokenValid('delete_chapter_' . $id, $request->request->get('_token'))) {
            $hasAssignments = $assignmentRepository->findOneBy(['chapter' => $chapter]) !== null;
            $hasItems = $chapterItemRepository->findOneBy(['chapter' => $chapter]) !== null;
            $hasProgress = $chapterProgressRepository->findOneBy(['chapter' => $chapter]) !== null;

            if ($hasAssignments || $hasItems || $hasProgress) {
                $this->addFlash('error', "Chapter «{$chapter->getTitle()}» has related records and cannot be deleted.");
                return $this->redirectToRoute('admin_chapters_index');
            }

            $title = $chapter->getTitle();
            $em->remove($chapter);
            $em->flush();
            $this->addFlash('success', "Chapter «{$title}» deleted.");
        }

        return $this->redirectToRoute('admin_chapters_index');
    }

    /**
     * @return array{
     *     errors: string[],
     *     classe: ?\App\Entity\Classe,
     *     subject: ?\App\Entity\Subject,
     *     title: string,
     *     description: ?string,
     *     sortOrder: ?int,
     *     isPublished: bool
     * }
     */
    private function parseChapterFormData(
        Request $request,
        ClasseRepository $classeRepository,
        SubjectRepository $subjectRepository,
    ): array
    {
        $errors = [];
        $classId = (int) $request->request->get('class_id');
        $subjectId = (int) $request->request->get('subject_id');
        $title = trim((string) $request->request->get('title', ''));
        $description = trim((string) $request->request->get('description', ''));
        $sortOrderRaw = trim((string) $request->request->get('sort_order', ''));
        $isPublished = (bool) $request->request->get('is_published', false);
        $classe = $classId ? $classeRepository->find($classId) : null;
        $subject = $subjectId ? $subjectRepository->find($subjectId) : null;
        $sortOrder = null;

        if (!$classe) {
            $errors[] = 'Please select a valid class.';
        }
        if (!$subject) {
            $errors[] = 'Please select a valid subject.';
        }
        if ($title === '') {
            $errors[] = 'Chapter title is required.';
        }
        if ($sortOrderRaw !== '') {
            $parsedSortOrder = filter_var($sortOrderRaw, FILTER_VALIDATE_INT);
            if ($parsedSortOrder === false) {
                $errors[] = 'Sort order must be a valid integer.';
            } else {
                $sortOrder = (int) $parsedSortOrder;
            }
        }

        return [
            'errors' => $errors,
            'classe' => $classe,
            'subject' => $subject,
            'title' => $title,
            'description' => $description !== '' ? $description : null,
            'sortOrder' => $sortOrder,
            'isPublished' => $isPublished,
        ];
    }

    /**
     * @param array{
     *     classe: ?\App\Entity\Classe,
     *     subject: ?\App\Entity\Subject,
     *     title: string,
     *     description: ?string,
     *     sortOrder: ?int,
     *     isPublished: bool
     * } $formData
     */
    private function applyChapterFormData(Chapter $chapter, array $formData): void
    {
        $chapter->setClasse($formData['classe']);
        $chapter->setSubject($formData['subject']);
        $chapter->setTitle($formData['title']);
        $chapter->setDescription($formData['description']);
        $chapter->setSortOrder($formData['sortOrder']);
        $chapter->setIsPublished($formData['isPublished']);
    }

    /**
     * @param array<string, int|string|null> $params
     */
    private function appendQueryParams(string $path, array $params): string
    {
        $parts = parse_url($path);
        if (!is_array($parts)) {
            return $path;
        }

        $query = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '') {
                $query[$key] = $value;
            }
        }

        $basePath = $parts['path'] ?? $path;
        $queryString = http_build_query($query);

        return $queryString === '' ? $basePath : $basePath . '?' . $queryString;
    }
}
