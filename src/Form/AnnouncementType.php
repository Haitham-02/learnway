<?php

namespace App\Form;

use App\Entity\Announcement;
use App\Entity\Classe;
use App\Repository\ClasseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnouncementType extends AbstractType
{
    private $classeRepository;

    public function __construct(ClasseRepository $classeRepository)
    {
        $this->classeRepository = $classeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $gradeLevels = $this->classeRepository->createQueryBuilder('c')
            ->select('DISTINCT c.grade_level')
            ->orderBy('c.grade_level', 'ASC')
            ->getQuery()
            ->getResult();

        $gradeChoices = [];
        foreach ($gradeLevels as $gl) {
            if ($gl['grade_level']) {
                $gradeChoices[$gl['grade_level']] = $gl['grade_level'];
            }
        }

        $builder
            ->add('title', TextType::class, [
                'attr' => ['class' => 'm3-input'],
            ])
            ->add('content', TextareaType::class, [
                'attr' => ['class' => 'm3-input', 'rows' => 5],
            ])
            ->add('priority', ChoiceType::class, [
                'choices' => [
                    'Normal' => 'NORMAL',
                    'High' => 'HIGH',
                    'Urgent' => 'URGENT',
                ],
                'attr' => ['class' => 'm3-input'],
            ])
            ->add('target_type', ChoiceType::class, [
                'choices' => [
                    'Entire School' => 'SCHOOL',
                    'Specific Grade Level' => 'GRADE',
                    'Specific Class' => 'CLASS',
                ],
                'attr' => ['class' => 'm3-input', 'onchange' => 'toggleTargetFields(this.value)'],
            ])
            ->add('target_value', ChoiceType::class, [
                'label' => 'Target Grade Level',
                'choices' => $gradeChoices,
                'required' => false,
                'placeholder' => 'Select Grade Level',
                'attr' => ['class' => 'm3-input target-field grade-field'],
            ])
            ->add('target_id', EntityType::class, [
                'label' => 'Target Class',
                'class' => Classe::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Select Class',
                'attr' => ['class' => 'm3-input target-field class-field'],
            ])
            ->add('publish_at', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'm3-input'],
            ])
            ->add('expire_at', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'm3-input'],
            ])
            ->add('is_pinned', ChoiceType::class, [
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'expanded' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Announcement::class,
        ]);
    }
}
