<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Controller\JsonApiController;
use App\Exception\ValidationException;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\ORMException;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateController extends JsonApiController
{
    private StudentRepository $repository;

    public function __construct(StudentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request, string $studentId)
    {
        try {
            $content = $this->getJsonContent($request);
            $student = $this->repository->updateFromRequest($content, $studentId);
            $this->repository->save($student);

            return $this->getJsonStandardResponse(Response::HTTP_NO_CONTENT);
        } catch (JsonException | ValidationException $exception) {
            return $this->json(['message' => PostController::BAD_REQUEST_MESSAGE], Response::HTTP_BAD_REQUEST);
        } catch (EntityNotFoundException $exception) {
            return $this->getJsonStandardResponse(Response::HTTP_NOT_FOUND);
        } catch (ORMException $exception) {
            return $this->getJsonStandardResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}