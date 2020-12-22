<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Mark;
use App\Entity\Student;

class AverageMarkService
{
    public function calculate(Student ...$students): ?float
    {
        $averages = [];
        foreach ($students as $student) {
            $marks = $student->getMarks()->toArray();
            if (empty($marks)) {
                continue;
            }

            $values = array_map(
                function (Mark $mark) {
                    return $mark->getValue();
                },
                $marks
            );

            $averages[] = $this->getAverage($values);
        }

        if (empty($averages)) {
            return null;
        }

        return $this->getAverage($averages);
    }

    /**
     * @param float[] $values
     */
    private function getAverage(array $values): float
    {
        return round(array_sum($values) / count($values), 2);
    }
}
