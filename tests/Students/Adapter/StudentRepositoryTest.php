<?php

declare(strict_types=1);

namespace App\Tests\Students\Adapter;

use App\Entity\Student;
use App\Exception\ValidationException;
use App\Repository\StudentRepository;
use Iterator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StudentRepositoryTest extends KernelTestCase
{
    private StudentRepository $subject;

    protected function setUp(): void
    {
        self::bootKernel();
        $subject = self::$container->get(StudentRepository::class);
        $this->assertInstanceOf(StudentRepository::class, $subject);
        $this->subject = $subject;
    }

    public function testBuildFromRequest()
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
    }

    /**
     * @dataProvider invalidStudentProvider
     */
    public function testInvalidDataThrowsValidationException(array $studentValues)
    {
        $this->expectException(ValidationException::class);

        $this->subject->createFromRequest($studentValues);
    }

    public function invalidStudentProvider(): Iterator
    {
        // empty array
        yield [
            []
        ];

        // missing or misspelled fields
        yield [
            [
                'first_name' => 'Ludovic',
                'birth_date' => '07/01/1982',
            ]
        ];

        yield [
            [
                'last_name' => 'REUS',
                'birth_date' => '07/01/1982',
            ]
        ];

        yield [
            [
                'first_name' => 'Ludovic',
                'last_name' => 'REUS',
            ]
        ];

        // empty values
        yield [
            [
                'first_name' => '  ',
                'last_name' => 'REUS',
                'birth_date' => '07/01/1982',
            ]
        ];

        yield [
            [
                'first_name' => 'Ludovic',
                'last_name' => '  ',
                'birth_date' => '07/01/1982',
            ]
        ];

        // Invalid format Date
        yield [
            [
                'first_name' => 'Ludovic',
                'last_name' => 'REUS',
                'birth_date' => '07-01-1982',
            ]
        ];
    }
}