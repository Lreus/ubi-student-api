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
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class UpdateControllerTest extends ClientAwareTestCase
{
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