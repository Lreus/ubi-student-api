<?php

declare(strict_types=1);

namespace App\Tests\Controller\Student;

use App\Repository\StudentRepository;
use App\Tests\ClientAwareTestCase;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DeleteControllerTest extends ClientAwareTestCase
{
    /**
     * Given I delete any Student update
     *
     * Then the student is removed from database
     * And Controller returns a Json Response
     * And response status code is 204 (Not Content)
     */
    public function testUpdateFromContentReturns204OnValidRequest()
    {
        $mock = $this->injectMockIntoClient(StudentRepository::class);

        $studentToRemove = 'the_student_to_delete';
        $mock->expects($this->once())->method('remove')->with($studentToRemove);

        $client = $this->deleteStudent($studentToRemove);

        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider ormExceptionProvider
     *
     * Given I delete any Student update
     * And StudentRepository throws an OrmException on remove
     *
     * Then Controller returns a Json Response
     * And response status code is 500 (Internal Server Error)
     * And response content is an array
     * And response[message] is equal "Internal Server Error"
     */
    public function testOrmExceptionReturnsSanitizedMessage(ORMException $exception)
    {
        $mock = $this->injectMockIntoClient(StudentRepository::class);
        $mock->method('remove')->willThrowException($exception);

        $this->client = $this->deleteStudent();
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
    private function deleteStudent(string $studentId = 'who-cares'): KernelBrowser
    {
        $this->client->request(
            Request::METHOD_DELETE,
            sprintf('/api/student/%s', $studentId),
            [],
            [],
            [
                'HTTP_Accept' => 'application/json'
            ]
        );

        return $this->client;
    }
}