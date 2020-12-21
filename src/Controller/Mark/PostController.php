<?php

declare(strict_types=1);

namespace App\Controller\Mark;

use App\Controller\JsonApiController;
use App\Exception\ValidationException;
use App\Repository\MarkRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\ORMException;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends JsonApiController
{
    const BAD_REQUEST_MESSAGE = 'Required fields: "value" :numeric, "subject": string';

    private StudentRepository $studentRepository;
    /**
     * @var MarkRepository
     */
    private MarkRepository $markRepository;

    public function __construct(
        StudentRepository $studentRepository,
        MarkRepository $markRepository
    ) {
        $this->studentRepository = $studentRepository;
        $this->markRepository = $markRepository;
    }

    public function __invoke(Request $request, string $studentId)
    {
        try {
            $content = $this->getJsonContent($request);
            $student = $this->studentRepository->require($studentId);
            $mark = $this->markRepository->createFromRequest($content, $student);
            $this->markRepository->save($mark);
            return new Response();
        } catch (JsonException|ValidationException $exception) {
            return $this->json(['message' => self::BAD_REQUEST_MESSAGE], Response::HTTP_BAD_REQUEST);
        } catch (EntityNotFoundException $exception) {
            return $this->getJsonStandardResponse(Response::HTTP_NOT_FOUND);
        } catch (ORMException $exception) {
            return $this->getJsonStandardResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        }


    }
}