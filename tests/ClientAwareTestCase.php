<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Iterator;
use PHPUnit\Framework\MockObject\MockObject;
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

    public function getTestClient(): KernelBrowser
    {
        if (!($this->client instanceof KernelBrowser)) {
            $this->tearDown();
            $this->setUp();
        }

        return $this->client;
    }

    public function injectMockIntoClient(string $mockedClass): MockObject
    {
        $mock = $this->getMockBuilder($mockedClass)
            ->disableOriginalConstructor()
            ->getMock();

        $this->getTestClient()->getContainer()->set($mockedClass, $mock);

        return $mock;
    }

    public function ormExceptionProvider(): Iterator
    {
        yield [new ORMException()];
        yield [new OptimisticLockException('optimistic exception', null)];
    }
}
