<?php

declare(strict_types=1);

namespace App\Controller;

use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class JsonApiController extends AbstractController
{
    /**
     * @throws JsonException
     */
    protected function getJsonContent(Request $request): array
    {
        $content = json_decode($request->getContent(), true);
        if (null === $content) {
            throw new JsonException();
        }

        return $content;
    }

    protected function getJsonStandardResponse(int $httpCode): JsonResponse
    {
        return $this->json(
            ['message' => Response::$statusTexts[$httpCode]],
            $httpCode
        );
    }
}