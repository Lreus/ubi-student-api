<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;

class Student
{
    private string $id;
    private string $lastName;
    private string $firstName;
    private DateTimeImmutable $birthDate;

    public function __construct(
        string $id,
        string $lastName,
        string $firstName,
        DateTimeImmutable $birthDate
    ) {
        $this->id = $id;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->birthDate = $birthDate;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getBirthDate(): DateTimeImmutable
    {
        return $this->birthDate;
    }
}