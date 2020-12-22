<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use App\Entity\Student;
use DateTimeImmutable;

class ObjectModelFactory
{
    public function buildAnyStudent(): Student
    {
        return new Student(
            'any_uuid',
            'Doe',
            'John',
            DateTimeImmutable::createFromFormat('d/m/Y', '02/12/1982'),
        );
    }
}