<?php

namespace App\Controller;

use App\Services\MappingService;
use App\Services\RoutingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class RoutingController extends AbstractController
{
    public function __construct(
        private readonly RoutingService $routingService,
        private readonly MappingService $mappingService
    )
    {
    }

    #[Route('/routing/{origin}/{destination}', name: 'app_get_route')]
    public function getRoute(string $origin, string $destination): JsonResponse
    {
        try {
            $route = $this->routingService->getRoute($origin, $destination);
        }
        catch (\Throwable $throwable) {
            return new JsonResponse(
                "Unexpected Failure",
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        if(is_null($route)) {
            return new JsonResponse(
                "No route found",
                Response::HTTP_BAD_REQUEST
            );
        }
        return new JsonResponse(
            $this->mappingService->mapRoute($route),
            Response::HTTP_OK
        );
    }
}