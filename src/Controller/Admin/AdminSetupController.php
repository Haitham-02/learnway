<?php

namespace App\Controller\Admin;

use App\Entity\AcademicYear;
use App\Entity\Classe;
use App\Entity\Subject;
use App\Entity\Term;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/setup', name: 'admin_setup_')]
#[IsGranted('ROLE_ADMIN')]
class AdminSetupController extends AbstractController
{
    #[Route('/new-year', name: 'new_year', methods: ['GET', 'POST'])]
    public function newYear(
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $errors = [];
        $result = null;

        if ($request->isMethod('POST')) {
            $yearName = trim((string) $request->request->get('year_name', ''));
            $startDateRaw = trim((string) $request->request->get('year_start_date', ''));
            $endDateRaw = trim((string) $request->request->get('year_end_date', ''));
            $yearIsCurrent = (bool) $request->request->get('year_is_current', false);

            $termsRaw = trim((string) $request->request->get('terms_lines', ''));
            $classesRaw = trim((string) $request->request->get('classes_lines', ''));
            $subjectsRaw = trim((string) $request->request->get('subjects_lines', ''));

            $startDate = $startDateRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $startDateRaw) ?: null) : null;
            $endDate = $endDateRaw !== '' ? (\DateTime::createFromFormat('Y-m-d', $endDateRaw) ?: null) : null;
            if ($yearName === '') {
                $errors[] = 'Academic year name is required.';
            }
            if (!$startDate || !$endDate) {
                $errors[] = 'Academic year start/end dates are required and must be valid.';
            } elseif ($endDate < $startDate) {
                $errors[] = 'Academic year end date cannot be earlier than start date.';
            }
            if ($termsRaw === '') {
                $errors[] = 'At least one term line is required.';
            }
            if ($classesRaw === '') {
                $errors[] = 'At least one class line is required.';
            }
            if ($subjectsRaw === '') {
                $errors[] = 'At least one subject line is required.';
            }

            $parsedTerms = $this->parseTermLines($termsRaw, $errors);
            $parsedClasses = $this->parseClassLines($classesRaw, $errors);
            $parsedSubjects = $this->parseSubjectLines($subjectsRaw, $errors);
            $currentTermCount = count(array_filter($parsedTerms, static fn(array $term): bool => $term['isCurrent']));
            if ($currentTermCount > 1) {
                $errors[] = 'Only one term can be marked as current in the setup input.';
            }

            if ($errors === []) {
                $year = new AcademicYear();
                $year->setName($yearName);
                $year->setStartDate($startDate);
                $year->setEndDate($endDate);
                $year->setIsCurrent($yearIsCurrent);
                $year->setCreatedAt(new \DateTime());

                if ($yearIsCurrent) {
                    foreach ($em->getRepository(AcademicYear::class)->findBy(['is_current' => true]) as $currentYear) {
                        $currentYear->setIsCurrent(false);
                    }
                }

                $em->persist($year);

                $terms = [];
                foreach ($parsedTerms as $termData) {
                    $term = new Term();
                    $term->setAcademicYear($year);
                    $term->setName($termData['name']);
                    $term->setStartDate($termData['start']);
                    $term->setEndDate($termData['end']);
                    $term->setIsCurrent($termData['isCurrent']);
                    $em->persist($term);
                    $terms[] = $term;
                }

                $classes = [];
                foreach ($parsedClasses as $classData) {
                    $existing = $em->getRepository(Classe::class)->findOneBy([
                        'name' => $classData['name'],
                        'grade_level' => $classData['gradeLevel'],
                        'section' => $classData['section'],
                    ]);
                    if ($existing) {
                        $classes[] = $existing;
                    } else {
                        $class = new Classe();
                        $class->setName($classData['name']);
                        $class->setGradeLevel($classData['gradeLevel']);
                        $class->setSection($classData['section']);
                        $class->setIsActive(true);
                        $class->setCreatedAt(new \DateTime());
                        $em->persist($class);
                        $classes[] = $class;
                    }
                }

                $createdSubjectsCount = 0;
                foreach ($parsedSubjects as $subjectData) {
                    // Create this subject for EACH term of the new year
                    foreach ($terms as $term) {
                        $subject = new Subject();
                        $subject->setSubjectCode($subjectData['code']);
                        $subject->setName($subjectData['name']);
                        $subject->setGradeLevel($subjectData['gradeLevel']);
                        $subject->setDescription($subjectData['description']);
                        $subject->setIsActive(true);
                        $subject->setTerm($term);
                        $em->persist($subject);
                        $createdSubjectsCount++;
                    }
                }

                $em->flush();
                $this->addFlash('success', sprintf(
                    'Guided setup completed! Created year "%s" with %d terms, %d classes, and %d term-specific subjects.',
                    $year->getName(),
                    count($terms),
                    count($classes),
                    $createdSubjectsCount
                ));
                return $this->redirectToRoute('admin_setup_new_year');
            }
        }

        return $this->render('admin/setup/new_year.html.twig', [
            'errors' => $errors,
            'result' => $result,
        ]);
    }

    /**
     * @param list<string> $errors
     * @return list<array{name:string,start:\DateTime,end:\DateTime,isCurrent:bool}>
     */
    private function parseTermLines(string $raw, array &$errors): array
    {
        $terms = [];
        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parts = array_map('trim', explode('|', $line));
            if (count($parts) < 3) {
                $errors[] = sprintf('Term line %d must be: Name|YYYY-MM-DD|YYYY-MM-DD|optional_current(1/0).', $index + 1);
                continue;
            }

            $start = \DateTime::createFromFormat('Y-m-d', $parts[1]) ?: null;
            $end = \DateTime::createFromFormat('Y-m-d', $parts[2]) ?: null;
            if (!$start || !$end || $end < $start) {
                $errors[] = sprintf('Term line %d has invalid dates.', $index + 1);
                continue;
            }

            $isCurrent = isset($parts[3]) && in_array(strtolower($parts[3]), ['1', 'true', 'yes', 'current'], true);
            $terms[] = [
                'name' => $parts[0],
                'start' => $start,
                'end' => $end,
                'isCurrent' => $isCurrent,
            ];
        }

        return $terms;
    }

    /**
     * @param list<string> $errors
     * @return list<array{name:string,gradeLevel:string,section:?string}>
     */
    private function parseClassLines(string $raw, array &$errors): array
    {
        $classes = [];
        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parts = array_map('trim', explode('|', $line));
            if (count($parts) < 2 || $parts[0] === '' || $parts[1] === '') {
                $errors[] = sprintf('Class line %d must be: Name|Grade|optional_section.', $index + 1);
                continue;
            }

            $classes[] = [
                'name' => $parts[0],
                'gradeLevel' => $parts[1],
                'section' => $parts[2] ?? null,
            ];
        }

        return $classes;
    }

    /**
     * @param list<string> $errors
     * @return list<array{code:string,name:string,gradeLevel:?string,description:?string}>
     */
    private function parseSubjectLines(string $raw, array &$errors): array
    {
        $subjects = [];
        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parts = array_map('trim', explode('|', $line));
            if (count($parts) < 2 || $parts[0] === '' || $parts[1] === '') {
                $errors[] = sprintf('Subject line %d must be: CODE|Name|optional_grade|optional_description.', $index + 1);
                continue;
            }

            $gradeLevel = isset($parts[2]) && $parts[2] !== '' ? $parts[2] : null;
            $description = isset($parts[3]) && $parts[3] !== '' ? $parts[3] : null;

            $subjects[] = [
                'code' => strtoupper($parts[0]),
                'name' => $parts[1],
                'gradeLevel' => $gradeLevel,
                'description' => $description,
            ];
        }

        return $subjects;
    }
}
