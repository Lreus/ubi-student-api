<?php

declare(strict_types=1);

namespace App\Controller\Mark;

use App\Controller\JsonApiController;
use App\Repository\StudentRepository;
use App\Service\AverageMarkService;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class StudentAverageController extends JsonApiController
{
    private StudentRepository $repository;

    private AverageMarkService $service;

    public function __construct(StudentRepository $repository, AverageMarkService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function __invoke(string $studentId)
    {
        try {
            $student = $this->repository->require($studentId);
            $averageMark = $this->service->calculate($student);
            if (null === $averageMark) {
                return $this->getJsonStandardResponse(Response::HTTP_NO_CONTENT);
            }
        } catch (EntityNotFoundException $exception) {
            return $this->getJsonStandardResponse(Response::HTTP_NOT_FOUND);
        }
        return new Response();
    }
}