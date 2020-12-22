<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MarkRepository")
 */
class Mark
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string", length=36)
     */
    private string $id;

    /**
     * @ORM\Column(type="float")
     */
    private float $value;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private string $subject;

    /**
     * @ORM\ManyToOne(targetEntity="Student", inversedBy="marks", cascade={"persist"})
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     */
    private Student $student;

    public function __construct(
        string $id,
        float $value,
        string $subject,
        Student $student
    ) {
        $this->id = $id;
        $this->value = $value;
        $this->subject = ucfirst(strtolower($subject));
        $this->student = $student;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getStudent(): Student
    {
        return $this->student;
    }
}
