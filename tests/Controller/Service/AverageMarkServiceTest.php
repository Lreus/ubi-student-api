<?php

declare(strict_types=1);

namespace App\Tests\Controller\Service;

use App\Entity\Mark;
use App\Entity\Student;
use App\Service\AverageMarkService;
use DateTimeImmutable;
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

    public function testReturnAverageOfMarks()
    {
        $student = $this->getSingleStudent();

        $marks = [
            new Mark('mark', 12, 'Grammar', $student),
            new Mark('mark', 7.5, 'Grammar', $student),
            new Mark('mark', 19.8, 'Grammar', $student),
        ];

        foreach ($marks as $mark) {
            $student->getMarks()->add($mark);
        }

        $this->assertEquals(13.1, $this->subject->calculate($student));
    }
}