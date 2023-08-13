<?php

namespace App\Tests\Controller;


use App\Controller\RoutingController;
use App\Entity\Countries;
use App\Services\MappingService;
use App\Services\RoutingService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class RoutingControllerTest extends KernelTestCase
{
    private RoutingService|MockObject $routingService;
    private MappingService|MockObject $mappingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->routingService = $this->createMock(RoutingService::class);
        $this->mappingService = $this->createMock(MappingService::class);
        self::bootKernel();
    }


    public function test_getRoute_catchesException()
    {
        $origin = "origin";
        $destination = "destination";
        $this->routingService
            ->expects(self::once())
            ->method("getRoute")
            ->with($origin, $destination)
            ->willThrowException(new \Exception());
        $container = static::getContainer();
        $container->set(RoutingService::class, $this->routingService);
        $routingController = $container->get(RoutingController::class);
        $expected =  new JsonResponse(
            "Unexpected Failure",
            500
        );
        $this->assertEquals(
            $expected,
            $routingController->getRoute($origin, $destination)
        );
    }

    public function test_getRoute_catchesError()
    {
        $origin = "origin";
        $destination = "destination";
        $this->routingService
            ->expects(self::once())
            ->method("getRoute")
            ->with($origin, $destination)
            ->willThrowException(new \Exception());
        $container = static::getContainer();
        $container->set(RoutingService::class, $this->routingService);
        $routingController = $container->get(RoutingController::class);
        $expected =  new JsonResponse(
            "Unexpected Failure",
            500
        );
        $this->assertEquals(
            $expected,
            $routingController->getRoute($origin, $destination)
        );
    }

    public function test_getRoute_returnBadRequestIfNoRouteFound()
    {
        $origin = "origin";
        $destination = "destination";
        $this->routingService
            ->expects(self::once())
            ->method("getRoute")
            ->with($origin, $destination)
            ->willReturn(null);
        $container = static::getContainer();
        $container->set(RoutingService::class, $this->routingService);
        $routingController = $container->get(RoutingController::class);
        $expected = new JsonResponse(
            "No route found",
            400
        );
        $this->assertEquals(
            $expected,
            $routingController->getRoute($origin, $destination)
        );
    }

    public function test_getRoute_worksCOrrectly()
    {
        $origin = "origin";
        $destination = "destination";
        $countries = $this->createMock(Countries::class);
        $resultArray = ["resultArray"];
        $this->routingService
            ->expects(self::once())
            ->method("getRoute")
            ->with($origin, $destination)
            ->willReturn($countries);
        $this->mappingService
            ->expects(self::once())
            ->method("mapRoute")
            ->with($countries)
            ->willReturn($resultArray);
        $container = static::getContainer();
        $container->set(RoutingService::class, $this->routingService);
        $container->set(MappingService::class, $this->mappingService);
        $routingController = $container->get(RoutingController::class);
        $expected = new JsonResponse(
            $resultArray,
            200
        );
        $this->assertEquals(
            $expected,
            $routingController->getRoute($origin, $destination)
        );
    }
}