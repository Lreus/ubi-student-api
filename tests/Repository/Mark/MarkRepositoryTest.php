<?php

declare(strict_types=1);

namespace App\Tests\Repository\Mark;

use App\Repository\MarkRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MarkRepositoryTest extends KernelTestCase
{
    private MarkRepository $subject;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->subject = $this->getMarkRepository();
    }

    public function getMarkRepository(): MarkRepository
    {
        $subject = self::$container->get(MarkRepository::class);
        $this->assertInstanceOf(MarkRepository::class, $subject);

        /** @var MarkRepository $subject */
        return $subject;
    }

    public function testTrue()
    {
        $this->assertTrue(false);
    }

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
    }
}