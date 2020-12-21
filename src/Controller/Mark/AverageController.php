<?php

declare(strict_types=1);

namespace App\Controller\Mark;

use App\Controller\JsonApiController;
use App\Repository\StudentRepository;
use App\Service\AverageMarkService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AverageController extends JsonApiController
{
    private StudentRepository $repository;

    private AverageMarkService $service;

    public function __construct(StudentRepository $repository, AverageMarkService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function __invoke(): JsonResponse
    {
        $students = $this->repository->findAll();
        $averageMark = $this->service->calculate(...$students);

        if (null === $averageMark) {
            return $this->getJsonStandardResponse(Response::HTTP_NO_CONTENT);
        }

        return $this->json(['average' => $averageMark], Response::HTTP_OK);
    }
}