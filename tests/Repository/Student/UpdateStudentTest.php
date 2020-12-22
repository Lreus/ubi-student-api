<?php

declare(strict_types=1);

namespace App\Tests\Repository\Student;

use App\Entity\Student;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @covers \StudentRepository::updateFromRequest
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
        $existingStudent = $this->objectModelFactory->buildAnyStudent();

        $this->clearStudentFromDatabase($existingStudent->getId());

        $this->entityManager->persist($existingStudent);

        $this->entityManager->flush();

        $updatedContent = [
            'first_name' => 'Thierry',
            'last_name' => 'Lebon',
            'birth_date' => '06/02/1983',
        ];

        $result = $this->subject->updateFromRequest($updatedContent, $existingStudent->getId());

        $this->assertInstanceOf(Student::class, $result);
        $this->assertSame($result->getLastName(), strtoupper($updatedContent['last_name']));
        $this->assertSame($result->getFirstName(), ucfirst(strtolower($updatedContent['first_name'])));
        $this->assertSame($result->getBirthDate()->format('d/m/Y'), $updatedContent['birth_date']);
    }
}
