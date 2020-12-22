<?php

declare(strict_types=1);

namespace App\Tests\Controller\Student;

use App\Controller\Student\PostController;
use App\Entity\Student;
use App\Exception\ValidationException;
use App\Repository\StudentRepository;
use App\Tests\ClientAwareTestCase;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class PostControllerTest extends ClientAwareTestCase
{
    /**
     * Given I request a post Student
     * And StudentRepository creates a Student from request.
     *
     * Then StudentRepository::save is called
     * And Controller returns a Json Response
     * And response status code is 201 (created)
     * And response content is an array
     * And response[id] is equal to the student identifier
     */
    public function testCreatedStudentGenerateCreatedResponse()
    {
        $mock = $this->injectMockIntoClient(StudentRepository::class);
        $expectedStudent = $this->expectsThisMockWillReturnStudent($mock);
        $mock->expects($this->exactly(1))->method('save')->with($expectedStudent);

        $client = $this->postStudent();

        $response = $client->getResponse();
        $this->assertStringContainsString('application/json', $response->headers->get('Content-type'));
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        $decodedResponse = $this->decodeResponse();
        $this->assertSame($expectedStudent->getId(), $decodedResponse['id']);
    }

    /**
     * Given I request a post Student
     * And StudentRepository does not validate request content.
     *
     * Then Controller returns a Json Response
     * And response status code is 400 (Bad request)
     * And response content is an array
     * And response[message] is equal to:
     * """
     * Required fields: "last_name" :string, "first_name": string, "birth_date": date(DD-MM-YYYY)
     * """
     */
    public function testValidationExceptionReturnsBadRequest()
    {
        $mock = $this->injectMockIntoClient(StudentRepository::class);

        $mock->method('createFromRequest')->willThrowException(new ValidationException());

        $this->client = $this->postStudent();

        $response = $this->decodeResponse();
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
     *
     * Then Controller returns a Json Response
     * And response status code is 500 (Internal server error)
     * And response content is an array
     * And response[message] is equal to "Internal server error"
     */
    public function testOrmExceptionReturnsSanitizedMessage(ORMException $exception)
    {
        $mock = $this->injectMockIntoClient(StudentRepository::class);

        $this->expectsThisMockWillReturnStudent($mock);

        $mock->method('save')->willThrowException($exception);

        $this->client = $this->postStudent();
        $response = $this->decodeResponse();

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
        $this->assertSame(
            'Internal Server Error',
            $response['message']
        );
    }

    /**
     * Build a Student instance, configure given mock to return it and returns the instance.
     */
    private function expectsThisMockWillReturnStudent(MockObject $mockObject): Student
    {
        $expectedStudent = $this->objectModelFactory->buildAnyStudent();

        $mockObject->method('createFromRequest')->willReturn($expectedStudent);

        return $expectedStudent;
    }

    /**
     * Perform request in instance's client and returns client.
     */
    private function postStudent(array $postParameters = []): KernelBrowser
    {
        $this->client->request(
            'PUT',
            '/api/student',
            [],
            [],
            [
                'HTTP_Accept' => 'application/json',
            ],
            json_encode($postParameters)
        );

        return $this->client;
    }
}
