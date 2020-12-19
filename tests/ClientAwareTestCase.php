<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ClientAwareTestCase extends WebTestCase
{
    /** @var KernelBrowser $client */
    protected $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
        unset($this->client);
    }
}