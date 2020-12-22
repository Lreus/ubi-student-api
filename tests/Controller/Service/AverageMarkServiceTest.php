<?php

declare(strict_types=1);

namespace App\Tests\Controller\Service;

use App\Entity\Mark;
use App\Entity\Student;
use App\Service\AverageMarkService;
use App\Tests\Utils\ObjectModelFactory;
use Iterator;
use PHPUnit\Framework\TestCase;

class AverageMarkServiceTest extends TestCase
{
    private AverageMarkService $subject;

    private ObjectModelFactory $objectModelFactory;

    protected function setUp(): void
    {
        $this->subject = new AverageMarkService();
        $this->objectModelFactory = new ObjectModelFactory();
    }

    public function testReturnsNullOnNoMarks()
    {
        $student = $this->objectModelFactory->buildAnyStudent();

        $result = $this->subject->calculate($student);
        $this->assertNull($result);
    }

    public function testReturnsNullOnNoMarksForMultipleStudent()
    {
        $student1 = $this->objectModelFactory->buildAnyStudent();
        $student2 = $this->objectModelFactory->buildAnyStudent();

        $result = $this->subject->calculate($student1, $student2);
        $this->assertNull($result);
    }

    public function testReturnsAverageOfStudentAverageForMultipleStudent()
    {
        $student1 = $this->objectModelFactory->buildAnyStudent();
        $student2 = $this->objectModelFactory->buildAnyStudent();

        $this->hydrateStudentWithMarks($student1, 5, 8.7, 16.2, 15); //rounded avg 11.23
        $this->hydrateStudentWithMarks($student2, 0, 12, 8.5, 14.2); //rounded avg 8.68

        $this->assertEquals(9.96, $this->subject->calculate($student1, $student2)); // rounded 9.955
    }

    /**
     * @dataProvider markProvider
     */
    public function testReturnAverageOfMarks(float $expected, float ...$markValues)
    {
        $student = $this->objectModelFactory->buildAnyStudent();

        $this->hydrateStudentWithMarks($student, ...$markValues);

        $this->assertEquals($expected, $this->subject->calculate($student));
    }

    public function markProvider(): Iterator
    {
        // Classic
        yield [13.1, 12, 7.5, 19.8];

        // Integers
        yield [7, 9, 5, 8, 6];

        // Single Mark
        yield [5, 5];

        // Rounded superior
        yield [7.67, 9, 9, 5];

        // Exactly two decimals
        yield [5.75, 9, 7, 2, 5];

        // Rounded Inferior
        yield [4.33, 6, 1, 6];
    }

    private function hydrateStudentWithMarks(Student $student, float ...$markValues): Student
    {
        array_walk(
            $markValues,
            function (float $markValue) use ($student) {
                $student->getMarks()->add(
                    new Mark('mark', $markValue, 'Grammar', $student)
                );
            }
        );

        return $student;
    }
}
