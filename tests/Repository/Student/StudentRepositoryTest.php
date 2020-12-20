<?php

declare(strict_types=1);

namespace App\Tests\Repository\Student;

use App\Entity\Student;
use App\Repository\StudentRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityNotFoundException;

class StudentRepositoryTest extends AbstractStudentRepositoryTest
{
    /**
     * @covers StudentRepository::save()
     */
    public function testSavingEntity()
    {
        $student = new Student(
        'any_id',
        'REUS',
        'Ludovic',
        DateTimeImmutable::createFromFormat('d/m/Y', '07/01/1982')
        );

        $this->clearStudentFromDatabase($student->getId());

        $this->subject->save($student);

        $result = $this->studentEntityManager->find(Student::class, $student->getId());
        $this->assertInstanceOf(Student::class, $result);
    }

    /**
     * @covers StudentRepository::remove()
     */
    public function testRemoveStudentRemovesEntity()
    {
        $student = new Student(
            'any_id',
            'REUS',
            'Ludovic',
            DateTimeImmutable::createFromFormat('d/m/Y', '07/01/1982')
        );

        $this->clearStudentFromDatabase($student->getId());

        $this->studentEntityManager->persist($student);
        $this->studentEntityManager->flush();
        $this->assertInstanceOf(Student::class, $this->studentEntityManager->find(Student::class, $student->getId()));

        $this->subject->remove($student->getId());

        $this->assertNull($this->studentEntityManager->find(Student::class, $student->getId()));
    }

    public function testNotFoundEntityThrowsException()
    {
        $studentId = 'any_student_id';
        $this->clearStudentFromDatabase($studentId);
        $this->expectException(EntityNotFoundException::class);

        $this->subject->require($studentId);
    }
}