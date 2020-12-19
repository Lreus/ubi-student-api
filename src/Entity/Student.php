<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentRepository")
 */
class Student
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string", length=36)
     */
    private string $id;

    /** @ORM\Column(type="string", length=64) */
    private string $lastName;

    /** @ORM\Column(type="string", length=64) */
    private string $firstName;

    /** @ORM\Column(type="date_immutable") */
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