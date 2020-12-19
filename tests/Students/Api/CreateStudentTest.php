<?php

declare(strict_types=1);

namespace App\Tests\Students\Api;

use App\Controller\Student\PostController;
use App\Entity\Student;
use App\Exception\ValidationException;
use App\Repository\StudentRepository;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CreateStudentTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    private $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testCreatedStudentGenerateCreatedResponse()
    {
        $mock = $this->startStudentRepositoryMock();

        $expectedStudent = new Student(
            'anyId',
            'Doe',
            'John',
            new DateTimeImmutable()
        );

        $mock->method('createFromRequest')->willReturn($expectedStudent);
        $mock->expects($this->once())->method('save')->with($expectedStudent);

        $client = $this->postStudent();

        $response = $client->getResponse();
        $this->assertStringContainsString('application/json', $response->headers->get('Content-type'));
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        $decodedResponse = $this->decodeResponse($client);
        $this->assertSame($expectedStudent->getId(), $decodedResponse['id']);
    }

    public function testValidationExceptionReturnsBadRequest()
    {
        $mock = $this->startStudentRepositoryMock();

        $mock->method('createFromRequest')->willThrowException(new ValidationException());

        $this->client = $this->postStudent();

        $response = $this->decodeResponse($this->client);
        $this->assertSame(
            PostController::BAD_REQUEST_MESSAGE,
            $response['message']
        );
    }

    private function startStudentRepositoryMock(): MockObject
    {
        $mock = $this->getMockBuilder(StudentRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->client->getContainer()->set(StudentRepository::class, $mock);

        return $mock;
    }

    private function decodeResponse(KernelBrowser $client): array
    {
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);

        return $response;
    }

    private function postStudent(array $postParameters = []): KernelBrowser
    {
        $this->client->request(
            'PUT',
            '/api/student',
            [],
            [],
            [
                'HTTP_Accept' => 'application/json'
            ],
            json_encode($postParameters)
        );

        return $this->client;
    }
}