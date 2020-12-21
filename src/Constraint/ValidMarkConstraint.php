<?php

declare(strict_types=1);

namespace App\Constraint;

use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

class ValidMarkConstraint extends Constraint
{
    const NOT_FLOAT = 'notFloat';
    const OUT_OF_RANGE = 'outOfRange';
    const NOT_ROUNDED = 'notRounded';

    /**
     * @throws InvalidArgumentException
     */
    public function getMessage(string $reason): string
    {
        switch ($reason){
            case self::OUT_OF_RANGE:
                return $this->outOfRange;
            case self::NOT_ROUNDED:
                return $this->notRounded;
            case self::NOT_FLOAT:
                return $this->mustBeFloat;
            default:
                throw new InvalidArgumentException();
        }
    }

    public string $mustBeFloat = 'Value {{ string }} is not a valid int or float';

    public string $outOfRange = 'Value {{ string }} must be a value between 0 and 20';

    public string $notRounded = 'Value {{ string }} cannot have more than two decimals';
}