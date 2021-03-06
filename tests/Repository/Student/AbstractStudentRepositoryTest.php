<?php

declare(strict_types=1);

namespace App\Tests\Repository\Student;

use App\Entity\Student;
use App\Repository\StudentRepository;
use App\Tests\Utils\ObjectModelFactory;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ObjectManager;
use Iterator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractStudentRepositoryTest extends KernelTestCase
{
    protected StudentRepository $subject;

    protected ObjectManager $entityManager;

    protected ObjectModelFactory $objectModelFactory;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->subject = $this->getStudentRepository();

        $this->entityManager = $this->getEntityManager();
        $this->objectModelFactory = new ObjectModelFactory();
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

        /* @var StudentRepository $repository */
        return $repository;
    }

    protected function clearStudentFromDatabase(string $studentId): void
    {
        $existing = $this->entityManager->find(Student::class, $studentId);
        if (null === $existing) {
            return;
        }

        $this->entityManager->remove($existing);
        $this->entityManager->flush();
        $this->assertNull($this->entityManager->find(Student::class, $studentId));
    }

    public function invalidStudentProvider(): Iterator
    {
        // empty array
        yield [
            [],
        ];

        // missing or misspelled fields
        yield [
            [
                'first_name' => 'Ludovic',
                'birth_date' => '07/01/1982',
            ],
        ];

        yield [
            [
                'last_name' => 'REUS',
                'birth_date' => '07/01/1982',
            ],
        ];

        yield [
            [
                'first_name' => 'Ludovic',
                'last_name' => 'REUS',
            ],
        ];

        // empty values
        yield [
            [
                'first_name' => '  ',
                'last_name' => 'REUS',
                'birth_date' => '07/01/1982',
            ],
        ];

        yield [
            [
                'first_name' => 'Ludovic',
                'last_name' => '  ',
                'birth_date' => '07/01/1982',
            ],
        ];

        // Invalid format Date
        yield [
            [
                'first_name' => 'Ludovic',
                'last_name' => 'REUS',
                'birth_date' => '07-01-1982',
            ],
        ];

        // Invalid types
        yield [
            [
                'first_name' => 1,
                'last_name' => 10,
                'birth_date' => '07-01-1982',
            ],
        ];

        yield [
            [
                'first_name' => 'Ludovic',
                'last_name' => 'REUS',
                'birth_date' => '07-01-1982',
            ],
        ];
    }

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
    }
}
