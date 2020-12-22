<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public function __invoke()
    {
        $fileLocator = new FileLocator(__DIR__.'/../../public');
        $doc = $fileLocator->locate('doc.json', null, true);
        $content = file_get_contents($doc);
        return new JsonResponse($content, Response::HTTP_OK, [], true);
    }
}