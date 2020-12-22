<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class ValidationException extends ValidatorException
{
    public function __construct(?ConstraintViolationListInterface $violationList = null)
    {
        $message = 'Unable to validate data';

        if ($violationList instanceof ConstraintViolationList) {
            $message = sprintf(
                'Unable to validate data with validator, because of violation list below %s',
                (string) $violationList
            );
        }

        parent::__construct($message);
    }
}
