<?php

declare(strict_types=1);

namespace App\Tests\Repository\Mark;

use App\Entity\Mark;
use App\Entity\Student;
use App\Exception\ValidationException;
use App\Repository\MarkRepository;
use DateTimeImmutable;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MarkRepositoryTest extends KernelTestCase
{
    private MarkRepository $subject;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->subject = $this->getMarkRepository();
    }

    public function getMarkRepository(): MarkRepository
    {
        $subject = self::$container->get(MarkRepository::class);
        $this->assertInstanceOf(MarkRepository::class, $subject);

        /** @var MarkRepository $subject */
        return $subject;
    }

    public function testCreateFromRequest()
    {
        $requestContent = [
            'value' => 15.5,
            'subject' => 'Grammar'
        ];

        $providedStudent = new Student(
            'any_id',
            'any_name',
            'any_first_name',
            new DateTimeImmutable()
        );

        $result = $this->subject->createFromRequest($requestContent, $providedStudent);

        $this->assertInstanceOf(Mark::class, $result);

        $this->assertSame($requestContent['value'], $result->getValue());
        $this->assertSame($requestContent['subject'], $result->getSubject());
        $this->assertSame($providedStudent, $result->getStudent());

        $this->assertInstanceOf(UuidInterface::class, Uuid::fromString($result->getId()));
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testThrowsValidationExceptionOnInvalidData(array $requestContent)
    {
        $providedStudent = new Student(
            'any_id',
            'any_name',
            'any_first_name',
            new DateTimeImmutable()
        );

        $this->expectException(ValidationException::class);

        $this->subject->createFromRequest($requestContent, $providedStudent);
    }

    public function invalidDataProvider(): \Iterator
    {
        // Missing subject
        yield [
            [
                'value' => 19.99,
            ]
        ];

        // Missing value
        yield [
            [
                'subject' => 'Grammar',
            ]
        ];

        // Not a numeric value
        yield [
            [
                'value' => 'quinze',
                'subject' => 'Grammar'
            ]
        ];

        // Negative value
        yield [
            [
                'value' => -0.1,
                'subject' => 'Grammar'
            ]
        ];

        // too high value
        yield [
            [
                'value' => 20.1,
                'subject' => 'Grammar'
            ]
        ];

        // to much decimals
        yield [
            [
                'value' => 19.999,
                'subject' => 'Grammar'
            ]
        ];

        yield [
            [
                'value' => 19.994,
                'subject' => 'Grammar'
            ]
        ];

        // Subject not a string
        yield [
            [
                'value' => 19.99,
                'subject' => 15
            ]
        ];

        // Empty subject
        yield [
            [
                'value' => 19.99,
                'subject' => '   '
            ]
        ];
    }

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
    }
}