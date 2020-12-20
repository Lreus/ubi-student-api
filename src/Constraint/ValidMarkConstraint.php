<?php

declare(strict_types=1);

namespace App\Constraint;

use Symfony\Component\Validator\Constraint;

class ValidMarkConstraint extends Constraint
{
    public $message = 'Value {{ string }} is not a valid float between 0 and 20';
}