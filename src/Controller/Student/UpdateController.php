<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Exception\ValidationException;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateController extends AbstractController
{
    private StudentRepository $repository;

    public function __construct(StudentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request, string $studentId)
    {
        $parameters = json_decode($request->getContent(), true);

        try {
            $student = $this->repository->updateFromRequest($parameters, $studentId);
        } catch (ValidationException $exception) {
            return $this->json(['message' => PostController::BAD_REQUEST_MESSAGE], Response::HTTP_BAD_REQUEST);
        } catch (EntityNotFoundException $exception) {
            return $this->json(
                ['message' => Response::$statusTexts[Response::HTTP_NOT_FOUND]],
                Response::HTTP_NOT_FOUND
            );
        }
    }
}