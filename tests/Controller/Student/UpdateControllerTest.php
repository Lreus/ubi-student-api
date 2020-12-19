<?php

declare(strict_types=1);

namespace App\Tests\Controller\Student;

use App\Controller\Student\PostController;
use App\Exception\ValidationException;
use App\Repository\StudentRepository;
use App\Tests\ClientAwareTestCase;
use App\Tests\ContainerMockGenerator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class UpdateControllerTest extends ClientAwareTestCase
{
    private ?ContainerMockGenerator $mockGenerator = null;

    public function init()
    {
        if (!($this->mockGenerator instanceof ContainerMockGenerator)) {
            $this->mockGenerator = new ContainerMockGenerator();
        }
    }

    public function testValidationExceptionReturnsBadRequest()
    {
        $this->init();
        $mock = $this->mockGenerator->injectMockIntoClient($this->client, StudentRepository::class);

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
}