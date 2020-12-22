<?php

declare(strict_types=1);

namespace App\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidMarkConstraintValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     *
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!($constraint instanceof ValidMarkConstraint)) {
            throw new UnexpectedTypeException($constraint, ValidMarkConstraint::class);
        }

        if (!is_numeric($value)) {
            $this->addViolation($value, $constraint, ValidMarkConstraint::NOT_FLOAT);
        }

        if (is_numeric($value)) {
            $floatValue = floatval($value);

            if (0 > $floatValue || 20 < $floatValue) {
                $this->addViolation($value, $constraint, ValidMarkConstraint::OUT_OF_RANGE);
            }

            if ($floatValue !== round($floatValue, 2)) {
                $this->addViolation($value, $constraint, ValidMarkConstraint::NOT_ROUNDED);
            }
        }
    }

    /**
     * @param mixed $value
     */
    private function addViolation($value, ValidMarkConstraint $constraint, string $reason): void
    {
        $this->context->buildViolation($constraint->getMessage($reason))
            ->setParameter('{{ string }}', (string) $value)
            ->addViolation();
    }
}
