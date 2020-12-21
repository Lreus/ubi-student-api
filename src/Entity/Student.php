<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /** @ORM\OneToMany(targetEntity="Mark", mappedBy="student", cascade={"remove"}) */
    private Collection $marks;

    public function __construct(
        string $id,
        string $lastName,
        string $firstName,
        DateTimeImmutable $birthDate
    ) {
        $this->id = $id;
        $this->birthDate = $birthDate;
        $this->marks = new ArrayCollection();

        $this->setLastName($lastName);
        $this->setFirstName($firstName);
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

    /**
     * @return ArrayCollection|Collection|Mark[]
     */
    public function getMarks(): Collection
    {
        return $this->marks;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = strtoupper($lastName);
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = ucfirst(strtolower($firstName));
    }

    public function setBirthDate(DateTimeImmutable $birthDate): void
    {
        $this->birthDate = $birthDate;
    }
}