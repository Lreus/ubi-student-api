<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Student;
use App\Exception\ValidationException;
use App\Repository\StudentRepository;
use App\Tests\Repository\Student\AbstractStudentRepositoryTest;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ObjectManager;
use Iterator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StudentRepositoryTest extends AbstractStudentRepositoryTest
{
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
}