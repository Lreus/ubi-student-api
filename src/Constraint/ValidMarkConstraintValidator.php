<?php

declare(strict_types=1);

namespace App\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidMarkConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof ValidMarkConstraint)) {
            throw new UnexpectedTypeException($constraint, ValidMarkConstraint::class);
        }

        if (!is_float($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', (string) $value)
                ->addViolation();
        }
    }
}