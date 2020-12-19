<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Controller\JsonApiController;
use App\Exception\ValidationException;
use App\Repository\StudentRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends JsonApiController
{
    const BAD_REQUEST_MESSAGE = 'Required fields: "last_name" :string, "first_name": string, "birth_date": date(DD-MM-YYYY)';
    /**
     * @var StudentRepository
     */
    private StudentRepository $repository;

    public function __construct(StudentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        try {
            $student = $this->repository->createFromRequest($parameters);
        }  catch (ValidationException $exception) {
            return $this->json(['message' => self::BAD_REQUEST_MESSAGE, Response::HTTP_BAD_REQUEST]);
        }

        try {
            $this->repository->save($student);
        } catch (OptimisticLockException | ORMException $exception) {
            return $this->getJsonStandardResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['id' => $student->getId()], Response::HTTP_CREATED);
    }
}