<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Mark;
use App\Entity\Student;

class AverageMarkService
{
    public function calculate(Student $student): ?float
    {
        $marks = $student->getMarks()->toArray();
        if (empty($marks)) {
            return null;
        }

        $values = array_map(
            function (Mark $mark) {
                return $mark->getValue();
            },
            $marks
        );

        return round(array_sum($values)/count($marks), 2);
    }
}