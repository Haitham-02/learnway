<?php

namespace App\Controller\Teacher;

use App\Entity\Chapter;
use App\Repository\ChapterRepository;
use App\Repository\TeacherAssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\ChapterContent;
use App\Entity\ChapterFile;
use App\Entity\ChapterItem;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/teacher', name: 'teacher_')]
#[IsGranted('ROLE_TEACHER')]
class TeacherChapterController extends AbstractTeacherController
{
    // ─── CHAPTERS LIST ───────────────────────────────────────────

    #[Route('/subject/{subjectId}/class/{classId}', name: 'chapters')]
    public function chapters(
        int $subjectId,
        int $classId,
        TeacherAssignmentRepository $taRepo,
        ChapterRepository $chapterRepo,
    ): Response {
        $ta = $this->getTeacherAssignment($taRepo, $subjectId, $classId);

        $chapters = $chapterRepo->findBy(
            ['subject' => $subjectId, 'classe' => $classId],
            ['sort_order' => 'ASC', 'id' => 'ASC'],
        );

        return $this->render('teacher/chapters.html.twig', [
            'ta' => $ta,
            'chapters' => $chapters,
        ]);
    }

    // ─── CHAPTER CREATE ──────────────────────────────────────────

    #[Route('/subject/{subjectId}/class/{classId}/chapter/new', name: 'chapter_new', methods: ['GET', 'POST'])]
    public function chapterNew(
        int $subjectId,
        int $classId,
        Request $request,
        TeacherAssignmentRepository $taRepo,
        ChapterRepository $chapterRepo,
        EntityManagerInterface $em,
        \Symfony\Component\Mailer\MailerInterface $mailer,
        \App\Repository\StudentEnrollmentRepository $enrollRepo
    ): Response {
        $ta = $this->getTeacherAssignment($taRepo, $subjectId, $classId);
        $errors = [];

        if ($request->isMethod('POST')) {
            $title = trim($request->request->get('title', ''));
            $description = trim($request->request->get('description', ''));
            $sortOrder = (int) $request->request->get('sort_order', 0);
            $isPublished = $request->request->has('is_published');

            if ($title === '') {
                $errors[] = 'Title is required.';
            }

            if (empty($errors)) {
                $chapter = new Chapter();
                $chapter->setTitle($title);
                $chapter->setDescription($description ?: null);
                $chapter->setSortOrder($sortOrder ?: null);
                $chapter->setIsPublished($isPublished);
                $chapter->setClasse($ta->getClasse());
                $chapter->setSubject($ta->getSubject());
                $chapter->setCreatedAt(new \DateTime());
                $chapter->setUpdatedAt(new \DateTime());

                $em->persist($chapter);
                $em->flush();

                // Send email to students only if published
                if ($isPublished) {
                    $enrollments = $enrollRepo->findBy(['classe' => $classId]);
                    foreach ($enrollments as $enrollment) {
                        $student = $enrollment->getUser();
                        if ($student->getEmail()) {
                            $email = (new \Symfony\Component\Mime\Email())
                                ->from('noreply@learnway.app')
                                ->to($student->getEmail())
                                ->subject('New Chapter Added: ' . $chapter->getTitle())
                                ->html('<p>Hello ' . $student->getFirstName() . ',</p><p>A new chapter has been added to your ' . $ta->getSubject()->getName() . ' class.</p><p>Title: ' . $chapter->getTitle() . '</p>');
                            
                            $mailer->send($email);
                        }
                    }
                    $this->addFlash('success', 'Chapter created and students notified.');
                } else {
                    $this->addFlash('success', 'Chapter created as draft (students were not notified).');
                }
                return $this->redirectToRoute('teacher_chapters', [
                    'subjectId' => $subjectId,
                    'classId' => $classId,
                ]);
            }
        }

        return $this->render('teacher/chapter_form.html.twig', [
            'ta' => $ta,
            'chapter' => null,
            'errors' => $errors,
        ]);
    }

    // ─── CHAPTER EDIT ────────────────────────────────────────────

    #[Route('/subject/{subjectId}/class/{classId}/chapter/{chapterId}/edit', name: 'chapter_edit', methods: ['GET', 'POST'])]
    public function chapterEdit(
        int $subjectId,
        int $classId,
        int $chapterId,
        Request $request,
        TeacherAssignmentRepository $taRepo,
        ChapterRepository $chapterRepo,
        EntityManagerInterface $em,
    ): Response {
        $ta = $this->getTeacherAssignment($taRepo, $subjectId, $classId);
        $chapter = $chapterRepo->find($chapterId);

        if (!$chapter || $chapter->getSubject()?->getId() !== $subjectId || $chapter->getClasse()?->getId() !== $classId) {
            throw $this->createNotFoundException('Chapter not found.');
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            $title = trim($request->request->get('title', ''));
            $description = trim($request->request->get('description', ''));
            $sortOrder = (int) $request->request->get('sort_order', 0);
            $isPublished = $request->request->has('is_published');

            if ($title === '') {
                $errors[] = 'Title is required.';
            }

            if (empty($errors)) {
                $chapter->setTitle($title);
                $chapter->setDescription($description ?: null);
                $chapter->setSortOrder($sortOrder ?: null);
                $chapter->setIsPublished($isPublished);
                $chapter->setUpdatedAt(new \DateTime());

                $em->flush();

                $this->addFlash('success', 'Chapter updated.');
                return $this->redirectToRoute('teacher_chapters', [
                    'subjectId' => $subjectId,
                    'classId' => $classId,
                ]);
            }
        }

        return $this->render('teacher/chapter_form.html.twig', [
            'ta' => $ta,
            'chapter' => $chapter,
            'errors' => $errors,
        ]);
    }

    // ─── CHAPTER SHOW (DASHBOARD) ──────────────────────────────
    
    #[Route('/chapter/{chapterId}', name: 'chapter_show')]
    public function chapterShow(
        int $chapterId,
        ChapterRepository $chapterRepo,
        TeacherAssignmentRepository $taRepo,
    ): Response {
        $chapter = $chapterRepo->find($chapterId);

        if (!$chapter) {
            throw $this->createNotFoundException('Chapter not found.');
        }

        // Check if teacher owns the subject/class for this chapter
        $ta = $this->getTeacherAssignment($taRepo, $chapter->getSubject()->getId(), $chapter->getClasse()->getId());

        return $this->render('teacher/chapter_show.html.twig', [
            'chapter' => $chapter,
            'ta' => $ta,
        ]);
    }

    // ─── CHAPTER FILE ADD ────────────────────────────────────────

    #[Route('/chapter/{chapterId}/file/add', name: 'chapter_file_add', methods: ['POST'])]
    public function chapterFileAdd(
        int $chapterId,
        Request $request,
        ChapterRepository $chapterRepo,
        FileUploader $fileUploader,
        EntityManagerInterface $em,
    ): Response {
        $chapter = $chapterRepo->find($chapterId);
        if (!$chapter) throw $this->createNotFoundException();

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');

        if ($uploadedFile) {
            $originalName = $uploadedFile->getClientOriginalName();
            $mimeType = $uploadedFile->getClientMimeType();
            $size = $uploadedFile->getSize();

            $newFilename = $fileUploader->upload($uploadedFile, 'chapters/' . $chapterId);

            $chapterFile = new ChapterFile();
            $chapterFile->setFileName($originalName);
            $chapterFile->setFilePath('/uploads/chapters/' . $chapterId . '/' . $newFilename);
            $chapterFile->setFileType($mimeType);
            $chapterFile->setFileSize($size);
            $chapterFile->setUser($this->getUser());
            $chapterFile->setUploadedAt(new \DateTime());
            $chapterFile->setChapter($chapter);

            $em->persist($chapterFile);
            $em->flush();

            $this->addFlash('success', 'File uploaded successfully.');
        }

        return $this->redirectToRoute('teacher_chapter_show', ['chapterId' => $chapterId]);
    }

    // ─── CHAPTER CONTENT ADD ─────────────────────────────────────

    #[Route('/chapter/{chapterId}/content/add', name: 'chapter_content_add', methods: ['GET', 'POST'])]
    public function chapterContentAdd(
        int $chapterId,
        Request $request,
        ChapterRepository $chapterRepo,
        EntityManagerInterface $em,
    ): Response {
        $chapter = $chapterRepo->find($chapterId);
        if (!$chapter) throw $this->createNotFoundException();

        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $body = $request->request->get('body');

            if ($body) {
                $content = new ChapterContent();
                $content->setTitle($title ?: null);
                $content->setBody($body);
                $content->setUser($this->getUser());
                $content->setCreatedAt(new \DateTime());
                $content->setUpdatedAt(new \DateTime());
                $content->setChapter($chapter);

                $em->persist($content);
                $em->flush();

                $this->addFlash('success', 'Content added.');
                return $this->redirectToRoute('teacher_chapter_show', ['chapterId' => $chapterId]);
            }
        }

        return $this->render('teacher/content_form.html.twig', [
            'chapter' => $chapter,
            'content' => null,
        ]);
    }

    #[Route('/chapter/{chapterId}/content/{contentId}/edit', name: 'chapter_content_edit', methods: ['GET', 'POST'])]
    public function chapterContentEdit(
        int $chapterId,
        int $contentId,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $content = $em->getRepository(ChapterContent::class)->find($contentId);
        if (!$content) throw $this->createNotFoundException();

        if ($request->isMethod('POST')) {
            $content->setTitle($request->request->get('title') ?: null);
            $content->setBody($request->request->get('body'));
            $content->setUpdatedAt(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'Content updated.');
            return $this->redirectToRoute('teacher_chapter_show', ['chapterId' => $chapterId]);
        }

        return $this->render('teacher/content_form.html.twig', [
            'chapter' => $content->getChapter(),
            'content' => $content,
        ]);
    }

    #[Route('/chapter/{chapterId}/content/{contentId}/delete', name: 'chapter_content_delete', methods: ['POST'])]
    public function chapterContentDelete(
        int $chapterId,
        int $contentId,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $content = $em->getRepository(ChapterContent::class)->find($contentId);
        if (!$content) throw $this->createNotFoundException();

        if ($this->isCsrfTokenValid('delete_content_' . $contentId, $request->request->get('_token'))) {
            $em->remove($content);
            $em->flush();
            $this->addFlash('success', 'Content deleted.');
        }

        return $this->redirectToRoute('teacher_chapter_show', ['chapterId' => $chapterId]);
    }

    #[Route('/chapter/{chapterId}/file/{fileId}/delete', name: 'chapter_file_delete', methods: ['POST'])]
    public function chapterFileDelete(
        int $chapterId,
        int $fileId,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $file = $em->getRepository(ChapterFile::class)->find($fileId);
        if (!$file) throw $this->createNotFoundException();

        if ($this->isCsrfTokenValid('delete_file_' . $fileId, $request->request->get('_token'))) {
            // Optional: delete physical file
            $em->remove($file);
            $em->flush();
            $this->addFlash('success', 'File deleted.');
        }

        return $this->redirectToRoute('teacher_chapter_show', ['chapterId' => $chapterId]);
    }

    #[Route('/chapter/{chapterId}/item/add', name: 'chapter_item_add', methods: ['POST'])]
    public function chapterItemAdd(
        int $chapterId,
        Request $request,
        ChapterRepository $chapterRepo,
        EntityManagerInterface $em,
    ): Response {
        $chapter = $chapterRepo->find($chapterId);
        if (!$chapter) throw $this->createNotFoundException();

        $type = $request->request->get('type');
        if ($type) {
            $item = new ChapterItem();
            $item->setType($type);
            $item->setSortOrder((int)$request->request->get('sort_order', 0));
            $item->setChapter($chapter);

            $em->persist($item);
            $em->flush();

            $this->addFlash('success', 'Item added.');
        }

        return $this->redirectToRoute('teacher_chapter_show', ['chapterId' => $chapterId]);
    }

    #[Route('/chapter/{chapterId}/item/{itemId}/delete', name: 'chapter_item_delete', methods: ['POST'])]
    public function chapterItemDelete(
        int $chapterId,
        int $itemId,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $item = $em->getRepository(ChapterItem::class)->find($itemId);
        if (!$item) throw $this->createNotFoundException();

        if ($this->isCsrfTokenValid('delete_item_' . $itemId, $request->request->get('_token'))) {
            $em->remove($item);
            $em->flush();
            $this->addFlash('success', 'Item deleted.');
        }

        return $this->redirectToRoute('teacher_chapter_show', ['chapterId' => $chapterId]);
    }

    // ─── CHAPTER DELETE ──────────────────────────────────────────

    #[Route('/subject/{subjectId}/class/{classId}/chapter/{chapterId}/delete', name: 'chapter_delete', methods: ['POST'])]
    public function chapterDelete(
        int $subjectId,
        int $classId,
        int $chapterId,
        Request $request,
        TeacherAssignmentRepository $taRepo,
        ChapterRepository $chapterRepo,
        EntityManagerInterface $em,
    ): Response {
        $ta = $this->getTeacherAssignment($taRepo, $subjectId, $classId);
        $chapter = $chapterRepo->find($chapterId);

        if (!$chapter || $chapter->getSubject()?->getId() !== $subjectId || $chapter->getClasse()?->getId() !== $classId) {
            throw $this->createNotFoundException('Chapter not found.');
        }

        if ($this->isCsrfTokenValid('delete_chapter_' . $chapterId, $request->request->get('_token'))) {
            $em->remove($chapter);
            $em->flush();
            $this->addFlash('success', 'Chapter deleted.');
        }

        return $this->redirectToRoute('teacher_chapters', [
            'subjectId' => $subjectId,
            'classId' => $classId,
        ]);
    }

}
