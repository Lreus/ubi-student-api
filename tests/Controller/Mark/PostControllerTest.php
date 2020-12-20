<?php

declare(strict_types=1);

namespace App\Tests\Controller\Mark;

use App\Controller\Mark\PostController;
use App\Entity\Student;
use App\Exception\ValidationException;
use App\Repository\MarkRepository;
use App\Repository\StudentRepository;
use App\Tests\ClientAwareTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostControllerTest extends ClientAwareTestCase
{
    public function testCreatedMarkGenerateBadRequestOnValidationException()
    {
        $studentMock = $this->injectMockIntoClient(StudentRepository::class);
        $studentMock->method('require')->willReturn(new Student(
            'any_id',
            'who_cares',
            'it is mocked',
            new DateTimeImmutable()
        ));

        $mock = $this->injectMockIntoClient(MarkRepository::class);
        $mock->method('createFromRequest')->willThrowException(new ValidationException());

        $this->client = $this->postMark();

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $response = $this->decodeResponse();
        $this->assertSame(
            PostController::BAD_REQUEST_MESSAGE,
            $response['message']
        );
    }

    public function testStudentISLoadedFromRepository()
    {
        $studentId = 'any_student_id';
        $mock = $this->injectMockIntoClient(StudentRepository::class);

        $mock->expects($this->once())->method('require')->with($studentId);

        $this->postMark([], $studentId);
    }

    public function testCreatedMarkReturnsNotFoundOnUnknownStudent()
    {
        $studentId = 'any_student_id';
        $mock = $this->injectMockIntoClient(StudentRepository::class);

        $mock->method('require')->with($studentId)->willThrowException(new EntityNotFoundException());

        $client = $this->postMark([], $studentId);

        $this->assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $response = $this->decodeResponse();
        $this->assertSame(
            'Not Found',
            $response['message']
        );
    }

    public function postMark(array $postParameters = [], string $studentId = 'who_cares' ): KernelBrowser
    {
        $this->client->request(
        Request::METHOD_POST,
        sprintf('/api/student/%s/mark', $studentId),
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