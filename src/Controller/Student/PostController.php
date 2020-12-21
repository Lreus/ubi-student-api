<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Controller\JsonApiController;
use App\Exception\ValidationException;
use App\Repository\StudentRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends JsonApiController
{
    const BAD_REQUEST_MESSAGE = 'Required fields: "last_name" :string, "first_name": string, "birth_date": date(DD-MM-YYYY)';

    private StudentRepository $repository;

    public function __construct(StudentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $content = $this->getJsonContent($request);
            $student = $this->repository->createFromRequest($content);
            $this->repository->save($student);

            return $this->json(['id' => $student->getId()], Response::HTTP_CREATED);
        }  catch (JsonException | ValidationException $exception) {
            return $this->json(['message' => self::BAD_REQUEST_MESSAGE], Response::HTTP_BAD_REQUEST);
        } catch (ORMException $exception) {
            return $this->getJsonStandardResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}