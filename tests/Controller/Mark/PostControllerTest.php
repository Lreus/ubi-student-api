<?php

declare(strict_types=1);

namespace App\Tests\Controller\Mark;

use App\Controller\Mark\PostController;
use App\Entity\Mark;
use App\Entity\Student;
use App\Exception\ValidationException;
use App\Repository\MarkRepository;
use App\Repository\StudentRepository;
use App\Tests\ClientAwareTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostControllerTest extends ClientAwareTestCase
{
    public function testCreatedResponseIsGeneratedOnSuccess()
    {
        $this->injectMockIntoClient(StudentRepository::class);
        $student = $this->buildAnyStudent();

        $markRepositoryMock = $this->injectMockIntoClient(MarkRepository::class);
        $expectedMark = $this->expectsMockWillReturnMarkForStudent($markRepositoryMock, $student);

        $client = $this->postMark();

        $response = $client->getResponse();
        $this->assertStringContainsString('application/json', $response->headers->get('Content-type'));
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        $decodedResponse = $this->decodeResponse();
        $this->assertSame($expectedMark->getId(), $decodedResponse['id']);
    }

    /**
     * @dataProvider ormExceptionProvider
     */
    public function testOrmExceptionReturnsSanitizedMessage(ORMException $exception)
    {
        $this->injectMockIntoClient(StudentRepository::class);
        $student = $this->buildAnyStudent();

        $markRepositoryMock = $this->injectMockIntoClient(MarkRepository::class);
        $markRepositoryMock->method('createFromRequest')->willReturn(
            new Mark(
                'any_mark_id',
                19.1,
                'mathematics',
                $student
            )
        );

        $markRepositoryMock->method('save')->willThrowException($exception);

        $this->client = $this->postMark();
        $response = $this->decodeResponse();

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
        $this->assertSame(
            'Internal Server Error',
            $response['message']
        );
    }

    public function testCreatedMarkIsPersisted()
    {
        $this->injectMockIntoClient(StudentRepository::class);
        $student = $this->buildAnyStudent();

        $markRepositoryMock = $this->injectMockIntoClient(MarkRepository::class);
        $mark = $this->expectsMockWillReturnMarkForStudent($markRepositoryMock, $student);

        $markRepositoryMock->expects($this->once())->method('save')->with($mark);

        $this->client = $this->postMark();
    }

    public function expectsThisMockWillReturnStudent(MockObject $mock): Student
    {
        $student = $this->buildAnyStudent();
        $mock->method('require')->willReturn($student);

        return $student;
    }

    public function buildAnyStudent(): Student
    {
        return new Student(
            'any_id',
            'who_cares',
            'it is mocked',
            new DateTimeImmutable()
        );
    }

    public function testCreatedMarkGenerateBadRequestOnValidationException()
    {
        $this->injectMockIntoClient(StudentRepository::class);

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

    public function expectsMockWillReturnMarkForStudent(MockObject $mockObject, Student $student): Mark
    {
        $mockObject->method('createFromRequest')->willReturn(
            $mark = new Mark(
                'any_mark_id',
                19.1,
                'mathematics',
                $student
            )
        );

        return $mark;
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