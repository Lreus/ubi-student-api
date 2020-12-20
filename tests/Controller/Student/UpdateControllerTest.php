<?php

declare(strict_types=1);

namespace App\Tests\Controller\Student;

use App\Controller\Student\PostController;
use App\Entity\Student;
use App\Exception\ValidationException;
use App\Repository\StudentRepository;
use App\Tests\ClientAwareTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Iterator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class UpdateControllerTest extends ClientAwareTestCase
{
    /**
     * Given I post any Student update
     * And StudentRepository does not validate request content
     *
     * Then Controller returns a Json Response
     * And response status code is 400 (Bad Request)
     * And response content is an array
     * And response[message] is equal to bad request message on student creation
     */
    public function testValidationExceptionReturnsBadRequest()
    {
        $mock = $this->injectMockIntoClient(StudentRepository::class);

        $mock->method('updateFromRequest')->willThrowException(new ValidationException());

        $client = $this->updateStudent('existing_user');

        $this->assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $response = $this->decodeResponse();
        $this->assertSame(
            PostController::BAD_REQUEST_MESSAGE,
            $response['message']
        );
    }

    /**
     * Given I post any Student update
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

        $mock->method('updateFromRequest')->willThrowException(new EntityNotFoundException());

        $client = $this->updateStudent('unknown_user');

        $this->assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $response = $this->decodeResponse();
        $this->assertSame(
            'Not Found',
            $response['message']
        );
    }

    /**
     * Given I post any Student update
     * And StudentRepository returns an updated Student
     *
     * Then the student is saved in database
     * And Controller returns a Json Response
     * And response status code is 204 (Not Content)
     */
    public function testUpdateFromContentReturns204OnValidRequest()
    {
        $mock = $this->injectMockIntoClient(StudentRepository::class);

        $expectedStudent = $this->expectsThisMockWillReturnStudent($mock);
        $mock->expects($this->once())->method('save')->with($expectedStudent);

        $client = $this->updateStudent('the_updated_user');

        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider ormExceptionProvider
     *
     * Given I post any Student update
     * And StudentRepository throws an OrmException on save
     *
     * Then Controller returns a Json Response
     * And response status code is 500 (Internal Server Error)
     * And response content is an array
     * And response[message] is equal "Internal Server Error"
     */
    public function testOrmExceptionReturnsSanitizedMessage(ORMException $exception)
    {
        $mock = $this->injectMockIntoClient(StudentRepository::class);

        $this->expectsThisMockWillReturnStudent($mock);

        $mock->method('save')->willThrowException($exception);

        $this->client = $this->updateStudent('any_user_id');
        $response = $this->decodeResponse();

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
        $this->assertSame(
            'Internal Server Error',
            $response['message']
        );
    }

    /**
     * Perform request in instance's client and returns client.
     */
    private function updateStudent(string $studentId, array $postParameters = []): KernelBrowser
    {
        $this->client->request(
            'PUT',
            sprintf('/api/student/%s', $studentId),
            [],
            [],
            [
                'HTTP_Accept' => 'application/json'
            ],
            json_encode($postParameters)
        );

        return $this->client;
    }

    /**
     * Build a Student instance, configure given mock to return it and returns the instance.
     */
    private function expectsThisMockWillReturnStudent(MockObject $mockObject): Student
    {
        $expectedStudent = new Student(
            'any_id',
            'who cares ?',
            'it is mocked',
            new DateTimeImmutable()
        );

        $mockObject->expects($this->once())->method('updateFromRequest')->willReturn($expectedStudent);

        return $expectedStudent;
    }
}