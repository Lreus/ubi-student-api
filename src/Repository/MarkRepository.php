<?php

declare(strict_types=1);

namespace App\Repository;

use App\Constraint\ValidMarkConstraint;
use App\Entity\Mark;
use App\Entity\Student;
use App\Exception\ValidationException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class MarkRepository extends ServiceEntityRepository
{
    private ValidatorInterface $validator;

    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator)
    {
        $this->validator = $validator;

        parent::__construct($registry,Mark::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Mark ...$marks)
    {
        $em = $this->getEntityManager();
        foreach ($marks as $mark) {
            $em->persist($mark);
        }

        $em->flush();
    }

    /**
     * @throws ValidationException
     */
    public function createFromRequest(array $content, Student $student): Mark
    {
        $constraint = $this->loadRequestConstraint();

        $violationList = $this->validator->validate($content, $constraint);
        if (0 < count($violationList)) {
            throw new ValidationException($violationList);
        }

        return new Mark(
            Uuid::uuid4()->toString(),
            floatval($content['value']),
            $content['subject'],
            $student
        );
    }

    private function loadRequestConstraint(): Constraint
    {
        return new Assert\Collection([
            'fields' => [
                'value' => [
                    new ValidMarkConstraint(),
                ],
                'subject' => [
                    new Assert\Type([
                        'type' => 'string',
                    ]),
                    new Assert\NotBlank([
                            'normalizer' => 'trim'
                        ]
                    ),
                ],
            ],
        ]);
    }
}