<?php

declare(strict_types=1);

namespace App\Tests\Repository\Student;

use App\Entity\Student;
use App\Exception\ValidationException;
use App\Repository\StudentRepository;

/**
 * @covers StudentRepository::createFromRequest
 */
class CreateStudentTest extends AbstractStudentRepositoryTest
{
    public function testBuildFromRequest(): Student
    {
        $studentValues = [
            'first_name' => 'Ludovic',
            'last_name' => 'REUS',
            'birth_date' => '07/01/1982',
        ];

        $result = $this->subject->createFromRequest($studentValues);

        $this->assertInstanceOf(Student::class, $result);
        $this->assertSame($result->getLastName(), $studentValues['last_name']);
        $this->assertSame($result->getFirstName(), $studentValues['first_name']);
        $this->assertSame($result->getBirthDate()->format('d/m/Y'), $studentValues['birth_date']);

        return $result;
    }

    /**
     * @dataProvider invalidStudentProvider
     */
    public function testInvalidDataThrowsValidationExceptionOnCreation(array $studentValues)
    {
        $this->expectException(ValidationException::class);

        $this->subject->createFromRequest($studentValues);
    }
}