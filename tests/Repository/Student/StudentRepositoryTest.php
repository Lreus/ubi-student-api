<?php

declare(strict_types=1);

namespace App\Tests\Repository\Student;

use App\Entity\Mark;
use App\Entity\Student;
use App\Repository\MarkRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @coversDefaultClass \App\Repository\StudentRepository
 */
class StudentRepositoryTest extends AbstractStudentRepositoryTest
{
    /**
     * @covers ::save
     */
    public function testSavingEntity()
    {
        $student = $this->objectModelFactory->buildAnyStudent();

        $this->clearStudentFromDatabase($student->getId());

        $this->subject->save($student);

        $result = $this->entityManager->find(Student::class, $student->getId());
        $this->assertInstanceOf(Student::class, $result);
    }

    public function testCascadeRemove()
    {
        $student = $this->objectModelFactory->buildAnyStudent();
        $this->clearStudentFromDatabase($student->getId());

        foreach (['good_mark', 'bad_mark'] as $markId) {
            $entity = $this->entityManager->find(Mark::class, $markId);
            if (null !== $entity) {
                $this->entityManager->remove($entity);
            }
        }
        $this->entityManager->flush();

        $marks = [
            new Mark(
                'good_mark',
                20,
                'grammar',
                $student
            ),
            new Mark(
                'bad_mark',
                0.5,
                'arts',
                $student
            ),
        ];

        $markRepository = self::$container->get(MarkRepository::class);
        /* @var MarkRepository $markRepository */
        $this->assertInstanceOf(MarkRepository::class, $markRepository);
        $markRepository->save(...$marks);
        $this->entityManager->clear();

        $this->subject->remove($student->getId());
        $this->assertNull($this->entityManager->find(Student::class, $student->getId()));

        foreach ($marks as $mark) {
            $this->assertNull($this->entityManager->find(Mark::class, $mark->getId()));
        }
    }

    /**
     * @covers ::remove
     */
    public function testRemoveStudentRemovesEntities()
    {
        $student = $this->objectModelFactory->buildAnyStudent();

        $this->clearStudentFromDatabase($student->getId());

        $this->entityManager->persist($student);
        $this->entityManager->flush();
        $this->assertInstanceOf(Student::class, $this->entityManager->find(Student::class, $student->getId()));

        $this->subject->remove($student->getId());

        $this->assertNull($this->entityManager->find(Student::class, $student->getId()));
    }

    public function testNotFoundEntityThrowsException()
    {
        $studentId = 'any_student_id';
        $this->clearStudentFromDatabase($studentId);
        $this->expectException(EntityNotFoundException::class);

        $this->subject->require($studentId);
    }

    private function getAnyStudent(): Student
    {
        return new Student(
            'any_id',
            'REUS',
            'Ludovic',
            DateTimeImmutable::createFromFormat('d/m/Y', '07/01/1982')
        );
    }
}
