<?php

declare(strict_types=1);

namespace App\Tests\Repository\Student;

use App\Entity\Student;
use App\Exception\ValidationException;
use DateTimeImmutable;
use Doctrine\ORM\EntityNotFoundException;
use App\Repository\StudentRepository;

/**
 * @covers StudentRepository::updateFromRequest
 */
class UpdateStudentTest extends AbstractStudentRepositoryTest
{
    /**
     * @dataProvider invalidStudentProvider
     */
    public function testInvalidDataThrowsValidationExceptionOnUpdate(array $studentValues)
    {
        $this->expectException(ValidationException::class);

        $this->subject->updateFromRequest($studentValues, 'anyId');
    }

    public function testUnknownUserThrowEntityNotFoundException()
    {
        $userId = 'the_existing_user';
        $this->clearStudentFromDatabase($userId);

        $updatedContent = [
            'first_name' => 'Thierry',
            'last_name' => 'Lebon',
            'birth_date' => '06/02/1983',
        ];

        $this->expectException(EntityNotFoundException::class);

        $this->subject->updateFromRequest($updatedContent, $userId);
    }

    public function testUpdateFromRequest()
    {
        $userId = 'the_existing_user';
        $userLastName = 'Doe';
        $userFirstName = 'John';
        $userBirthDate = DateTimeImmutable::createFromFormat('d/m/Y', '02/12/1982');

        $this->clearStudentFromDatabase($userId);

        $this->entityManager->persist(
            new Student(
                $userId,
                $userLastName,
                $userFirstName,
                $userBirthDate
            )
        );

        $this->entityManager->flush();

        $updatedContent = [
            'first_name' => 'Thierry',
            'last_name' => 'Lebon',
            'birth_date' => '06/02/1983',
        ];

        $result = $this->subject->updateFromRequest($updatedContent, $userId);

        $this->assertInstanceOf(Student::class, $result);
        $this->assertSame($result->getLastName(), $updatedContent['last_name']);
        $this->assertSame($result->getFirstName(), $updatedContent['first_name']);
        $this->assertSame($result->getBirthDate()->format('d/m/Y'), $updatedContent['birth_date']);
    }
}