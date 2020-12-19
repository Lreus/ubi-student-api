<?php

declare(strict_types=1);

namespace App\Tests\Students\Api;

use App\Controller\Student\PostController;
use App\Entity\Student;
use App\Exception\ValidationException;
use App\Repository\StudentRepository;
use DateTimeImmutable;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Iterator;
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

    /**
     * Given I request a post Student
     * And StudentRepository creates a Student from request
     *
     * Then Studentrepository::save is called
     * And Controller returns a Json Response
     * And response status code is 201 created
     * And response content is an array
     * And response[id] is equal to the student identifier
     */
    public function testCreatedStudentGenerateCreatedResponse()
    {
        $mock = $this->startStudentRepositoryMock();

        $expectedStudent = $this->expectsThisMockWillReturnStudent($mock);

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

    /**
     * @dataProvider ormExceptionProvider
     *
     * Given I request a post Student
     * And StudentRepository creates a Student from request
     * And StudentRepository::Save throws an OrmException
     * Then Controller returns a Json Response
     * And response status code is 500 Internal server error
     * And response content is an array
     * And response[message] is equal to "Internal server error"
     */
    public function testOrmExceptionReturnsSanitizedMessage(Exception $exception)
    {
        $mock = $this->startStudentRepositoryMock();

        $this->expectsThisMockWillReturnStudent($mock);

        $mock->method('save')->willThrowException($exception);

        $this->client = $this->postStudent();
        $response = $this->decodeResponse($this->client);

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
        $this->assertSame(
            'Internal server error',
            $response['message']
        );
    }

    public function ormExceptionProvider(): Iterator
    {
        yield [new ORMException()];
        yield [new OptimisticLockException('optimistic exception', null)];
    }

    /**
     * Build a Student instance, configure given mock to return it and returns the instance.
     */
    private function expectsThisMockWillReturnStudent(MockObject $mockObject): Student
    {
        $expectedStudent = new Student(
            'anyId',
            'Doe',
            'John',
            new DateTimeImmutable()
        );

        $mockObject->method('createFromRequest')->willReturn($expectedStudent);

        return $expectedStudent;
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