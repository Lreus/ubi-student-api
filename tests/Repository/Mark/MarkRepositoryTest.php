<?php

declare(strict_types=1);

namespace App\Tests\Repository\Mark;

use App\Entity\Mark;
use App\Entity\Student;
use App\Exception\ValidationException;
use App\Repository\MarkRepository;
use App\Repository\StudentRepository;
use App\Tests\Utils\ObjectModelFactory;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Iterator;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MarkRepositoryTest extends KernelTestCase
{
    private MarkRepository $subject;

    protected ObjectModelFactory $objectModelFactory;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->subject = $this->getMarkRepository();
        $this->objectModelFactory = new ObjectModelFactory();
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

        $providedStudent = $this->objectModelFactory->buildAnyStudent();

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
        $providedStudent = $this->objectModelFactory->buildAnyStudent();

        $this->expectException(ValidationException::class);

        $this->subject->createFromRequest($requestContent, $providedStudent);
    }

    public function invalidDataProvider(): Iterator
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

    public function testSavingEntity()
    {
        $studentRepository = self::$container->get(StudentRepository::class);
        /** @var StudentRepository $studentRepository */
        $this->assertInstanceOf(StudentRepository::class, $studentRepository);

        $student = $this->objectModelFactory->buildAnyStudent();

        $studentRepository->remove($student->getId());

        $mark = new Mark(
            'another_id',
            12.8,
            'arts',
            $student
        );

        $entityManager = self::$container->get('doctrine')->getManager();
        $this->assertInstanceOf(ObjectManager::class, $entityManager);
        /** @var ObjectManager $entityManager */
        $existingMark = $entityManager->find(Mark::class, $mark->getId());
        if (null !== $existingMark) {
            $entityManager->remove($existingMark);
            $entityManager->flush();
        }

        $this->subject->save($mark);

        $result = $entityManager->find(Mark::class, 'another_id');

        $this->assertSame($mark, $result);
        $this->assertSame($mark->getStudent(), $student);
    }

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
    }
}