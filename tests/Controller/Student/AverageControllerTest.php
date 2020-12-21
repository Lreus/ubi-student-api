<?php

declare(strict_types=1);

namespace App\Tests\Controller\Student;

use App\Entity\Student;
use App\Repository\StudentRepository;
use App\Service\AverageMarkService;
use App\Tests\ClientAwareTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AverageControllerTest extends ClientAwareTestCase
{
    public function testCalculationIsDelegatedToMock()
    {
        $averageServiceMock = $this->injectMockIntoClient(AverageMarkService::class);
        $studentRepositoryMock = $this->injectMockIntoClient(StudentRepository::class);
        $student = new Student(
            'any_id',
            'any_name',
            'any_first_name',
            new DateTimeImmutable()
        );
        $studentRepositoryMock->method('require')->willReturn($student);

        $averageServiceMock->expects($this->once())->method('calculate')->with($student);

        $client = $this->getAverage();
    }

    /**
     * Given I get any Student scholastic average
     * And StudentRepository does not found the student
     *
     * Then Controller returns a Json Response
     * And response status code is 404 (Not Found)
     * And response content is an array
     * And response[message] is equal "Not Found"
     */
    public function testEntityNotFoundExceptionReturns404()
    {
        $mock = $this->injectMockIntoClient(StudentRepository::class);

        $mock->method('require')->willThrowException(new EntityNotFoundException());

        $client = $this->getAverage();

        $this->assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $response = $this->decodeResponse();
        $this->assertSame(
            'Not Found',
            $response['message']
        );
    }

    public function getAverage(string $studentId = 'who_cares'): KernelBrowser
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf('/api/student/%s/marks/average', $studentId),
            [],
            [],
            [
                'HTTP_Accept' => 'application/json'
            ]
        );

        return $this->client;
    }
}