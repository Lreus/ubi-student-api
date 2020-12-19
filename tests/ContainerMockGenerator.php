<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ContainerMockGenerator extends TestCase
{
    /**
     * Build a mock for provided class, inject it in the client container and returns created mock.
     */
    public function injectMockIntoClient(KernelBrowser $client, string $mockedClass): MockObject
    {
        $mock = $this->getMockBuilder($mockedClass)
            ->disableOriginalConstructor()
            ->getMock();

        $client->getContainer()->set($mockedClass, $mock);

        return $mock;
    }
}