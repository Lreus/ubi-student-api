<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Controller\JsonApiController;
use App\Repository\StudentRepository;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteController extends JsonApiController
{
    private StudentRepository $repository;

    public function __construct(StudentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(string $studentId): JsonResponse
    {
        try {
            $this->repository->remove($studentId);
        } catch (ORMException $exception) {
            return $this->getJsonStandardResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->getJsonStandardResponse(Response::HTTP_NO_CONTENT);
    }
}
