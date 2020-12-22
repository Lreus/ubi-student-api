<?php

declare(strict_types=1);

namespace App\Tests\Controller\Mark;

use App\Repository\StudentRepository;
use App\Service\AverageMarkService;
use App\Tests\ClientAwareTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MarksAverageControllerTest extends ClientAwareTestCase
{
    public function testAverageIsReturnedAfterCalculation()
    {
        $mock = $this->injectMockIntoClient(StudentRepository::class);
        $mock->method('findAll')->willReturn([]);

        $averageMock = $this->injectMockIntoClient(AverageMarkService::class);
        $expectedAverage = 0.16;
        $averageMock->method('calculate')->willReturn($expectedAverage);

        $client = $this->getAverage();

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString('application/json', $response->headers->get('Content-type'));
        $decodedResponse = $this->decodeResponse();
        $this->assertSame($expectedAverage, $decodedResponse['average']);
    }

    public function testNoContentIsReturnedOnStudentWithNoMark()
    {
        $mock = $this->injectMockIntoClient(StudentRepository::class);
        $mock->method('findAll')->willReturn([]);

        $averageServiceMock = $this->injectMockIntoClient(AverageMarkService::class);
        $averageServiceMock->method('calculate')->willReturn(null);

        $client = $this->getAverage();

        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }

    public function testPassAllStudentsToService()
    {
        $averageMock = $this->injectMockIntoClient(AverageMarkService::class);
        $mock = $this->injectMockIntoClient(StudentRepository::class);
        $students = [$this->objectModelFactory->buildAnyStudent()];

        $mock->expects($this->once())->method('findAll')->willReturn($students);
        $averageMock->expects($this->once())->method('calculate')->with(...$students);
        $this->getAverage();
    }

    public function getAverage(): KernelBrowser
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf('/api/marks/average'),
            [],
            [],
            [
                'HTTP_Accept' => 'application/json',
            ]
        );

        return $this->client;
    }
}
