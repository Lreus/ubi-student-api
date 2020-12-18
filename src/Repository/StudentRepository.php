<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Student;
use App\Exception\ValidationException;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StudentRepository
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @throws ValidationException
     */
    public function createFromRequest(array $content): Student
    {
        $content = array_map(
            function ($value) {
                return trim($value);
            },
            $content
        );

        $violations = $this->validator->validate(
            $content,
            $this->getInputConstraint()
        );

        if (0 < $violations->count()) {
            throw new ValidationException($violations);
        }

        return new Student(
            Uuid::uuid4()->toString(),
            $content['last_name'],
            $content['first_name'],
            DateTimeImmutable::createFromFormat('d/m/Y', $content['birth_date'])
        );
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