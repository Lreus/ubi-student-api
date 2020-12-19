<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ClientAwareTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    /**
     * Assert response was valid json and returns decoded result.
     */
    protected function decodeResponse(): array
    {
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($response);

        return $response;
    }

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
        unset($this->client);
    }
}
