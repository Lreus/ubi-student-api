<?php

declare(strict_types=1);

namespace App\Tests\Controller\Service;

use App\Entity\Mark;
use App\Entity\Student;
use App\Service\AverageMarkService;
use DateTimeImmutable;
use Iterator;
use PHPUnit\Framework\TestCase;

class AverageMarkServiceTest extends TestCase
{
    private AverageMarkService $subject;

    protected function setUp(): void
    {
        $this->subject = new AverageMarkService();
    }

    public function testReturnsNullOnNoMarks()
    {
        $student = $this->getSingleStudent();

        $result = $this->subject->calculate($student);
        $this->assertNull($result);
    }

    public function getSingleStudent(): Student
    {
        return new Student(
            'any_id',
            'lastName',
            'firstName',
            new DateTimeImmutable()
        );
    }

    /**
     * @dataProvider markProvider
     */
    public function testReturnAverageOfMarks(float $expected, float ...$markValues)
    {
        $student = $this->getSingleStudent();

        array_walk(
            $markValues,
            function (float $markValue) use($student) {
                $student->getMarks()->add(
                    new Mark('mark', $markValue, 'Grammar', $student)
                );
            }
        );

        $this->assertEquals($expected, $this->subject->calculate($student));
    }

    public function markProvider(): Iterator
    {
        // Classic
        yield [13.1, 12, 7.5, 19.8];

        // Integers
        yield [7, 9, 5, 8, 6];

        // Single Mark
        yield [ 5, 5 ];

        // Rounded superior
        yield [7.67, 9, 9, 5];

        // Exactly two decimals
        yield [5.75, 9, 7, 2, 5];

        // Rounded Inferior
        yield [4.33, 6, 1, 6];
    }
}