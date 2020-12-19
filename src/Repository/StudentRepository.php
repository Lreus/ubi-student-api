<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Student;
use App\Exception\ValidationException;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StudentRepository extends ServiceEntityRepository
{
    private ValidatorInterface $validator;

    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator)
    {
        $this->validator = $validator;

        parent::__construct($registry, Student::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Student ...$students)
    {
        $em = $this->getEntityManager();
        foreach ($students as $student) {
            $em->persist($student);
        }

        $em->flush();
    }

    /**
     * @throws ValidationException
     */
    public function createFromRequest(array $content): Student
    {
        $content = $this->sanitizeContent($content);

        $this->validateContent($content);

        return new Student(
            Uuid::uuid4()->toString(),
            $content['last_name'],
            $content['first_name'],
            DateTimeImmutable::createFromFormat('d/m/Y', $content['birth_date'])
        );
    }

    /**
     * @throws ValidationException
     * @throws EntityNotFoundException
     */
    public function updateFromRequest(array $content, string $userId)
    {
        $content = $this->sanitizeContent($content);

        $this->validateContent($content);

        $student = $this->find($userId);
        if (!($student instanceof Student)) {
            $message = sprintf('Unknown student identified by "%s"', $userId);
            throw new EntityNotFoundException($message);
        }
    }

    /**
     * @param String[] $content
     */
    private function sanitizeContent(array $content): array
    {
        return array_map(
            function ($value) {
                return trim($value);
            },
            $content
        );
    }

    /**
     * @throws ValidationException
     */
    private function validateContent(array $content): bool
    {
        $violations = $this->validator->validate(
            $content,
            $this->getInputConstraint()
        );

        if (0 < $violations->count()) {
            throw new ValidationException($violations);
        }

        return true;
    }

    private function getInputConstraint(): Constraint
    {
        return new Assert\Collection([
            'fields' => [
                'last_name' => new Assert\Required([
                    new Assert\NotBlank()
                ]),
                'first_name' => new Assert\Required([
                    new Assert\NotBlank()
                ]),
                'birth_date' => new Assert\Required([
                    new Assert\DateTime([
                        'format' => 'd/m/Y'
                    ])
                ]),
            ]
        ]);
    }
}