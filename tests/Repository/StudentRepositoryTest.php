<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Student;
use App\Exception\ValidationException;
use App\Repository\StudentRepository;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ObjectManager;
use Iterator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StudentRepositoryTest extends KernelTestCase
{
    private StudentRepository $subject;

    private ObjectManager $studentEntityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->subject = $this->getStudentRepository();

        $this->studentEntityManager = $this->getEntityManager();
    }

    private function getEntityManager(): ObjectManager
    {
        $doctrine = self::$container->get('doctrine');
        $this->assertInstanceOf(Registry::class, $doctrine);

        /** @var Registry $doctrine */
        $em = $doctrine->getManager();
        $this->assertInstanceOf(ObjectManager::class, $em);

        return $em;
    }

    private function getStudentRepository(): StudentRepository
    {
        $repository = self::$container->get(StudentRepository::class);
        $this->assertInstanceOf(StudentRepository::class, $repository);

        /** @var StudentRepository $repository */
        return $repository;
    }

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
     * @depends testBuildFromRequest
     */
    public function testSavingEntity(Student $student)
    {
        $this->clearStudentFromDatabase($student->getId());

        $this->subject->save($student);

        $result = $this->studentEntityManager->find(Student::class, $student->getId());
        $this->assertInstanceOf(Student::class, $result);
    }

    private function clearStudentFromDatabase(string $studentId): void
    {
        $existing = $this->studentEntityManager->find(Student::class, $studentId);
        if (null === $existing) {
            return;
        }

        $this->studentEntityManager->remove($existing);
        $this->studentEntityManager->flush();
        $this->assertNull($this->studentEntityManager->find(Student::class, $studentId));
    }

    /**
     * @dataProvider invalidStudentProvider
     */
    public function testInvalidDataThrowsValidationExceptionOnCreation(array $studentValues)
    {
        $this->expectException(ValidationException::class);

        $this->subject->createFromRequest($studentValues);
    }

    /**
     * @dataProvider invalidStudentProvider
     */
    public function testInvalidDataThrowsValidationExceptionOnUpdate(array $studentValues)
    {
        $this->expectException(ValidationException::class);

        $this->subject->updateFromRequest($studentValues, 'anyId');
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

        $this->studentEntityManager->persist(
            new Student(
                $userId,
                $userLastName,
                $userFirstName,
                $userBirthDate
            )
        );

        $this->studentEntityManager->flush();

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