<?php

namespace App\Application\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\Service\SomeService;

class DefaultController
{
    private $someService;

    public function __construct(SomeService $someService)
    {
        $this->someService = $someService;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        // Interactuar con los servicios de aplicación
        $data = $this->someService->getData();

        return new Response('Hello, World! Data: ' . $data);
    }
}
