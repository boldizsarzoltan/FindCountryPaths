<?php

namespace App\Tests\Services;


use App\Entity\Countries;
use App\Entity\Country;
use App\Repository\CountryRepository;
use App\Repository\CountryRepositoryFactory;
use App\Services\Exceptions\MalFormedDataException;
use App\Services\MappingService;
use App\Services\RoutingService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class RoutingServiceTest extends KernelTestCase
{
    private \PHPUnit\Framework\MockObject\MockObject|CountryRepositoryFactory $countryRepositoryFactory;
    private CountryRepository|\PHPUnit\Framework\MockObject\MockObject $countryRepository;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->countryRepositoryFactory = $this->createMock(CountryRepositoryFactory::class);
        $this->countryRepository = $this->createMock(CountryRepository::class);
    }

    public function test_mapRoute_returnsNull_ifOriginCountryNull()
    {
        $container = static::getContainer();
        $origin = "origin";
        $destination = "destination";
        $this->countryRepository
            ->expects(self::once())
            ->method("getCountryByCountryCode")
            ->with($origin)
            ->willReturn(null);
        $this->countryRepositoryFactory
            ->expects(self::once())
            ->method("getRepository")
            ->willReturn($this->countryRepository);
        $container->set(CountryRepositoryFactory::class, $this->countryRepositoryFactory);

        $routingService = $container->get(RoutingService::class);
        $this->assertNull(
            $routingService->getRoute($origin, $destination)
        );
    }

    public function test_mapRoute_returnsNull_ifDestinationCountryNull()
    {
        $container = static::getContainer();
        $origin = "origin";
        $originCountry = new Country("origin_name", $origin);
        $destination = "destination";
        $this->countryRepository
            ->expects(self::exactly(2))
            ->method("getCountryByCountryCode")
            ->withConsecutive(
                [$origin],
                [$destination]
            )
            ->willReturnOnConsecutiveCalls(
                $originCountry,
                null
            );
        $this->countryRepositoryFactory
            ->expects(self::exactly(2))
            ->method("getRepository")
            ->willReturn($this->countryRepository);
        $container->set(CountryRepositoryFactory::class, $this->countryRepositoryFactory);

        $routingService = $container->get(RoutingService::class);
        $this->assertNull(
            $routingService->getRoute($origin, $destination)
        );
    }

    public function test_mapRoute_returnsNull_ifRouteIsImpossible()
    {
        $container = static::getContainer();
        $origin = "origin";
        $originCountry = new Country("origin_name", $origin);
        $destination = "destination";
        $destinationCountry = new Country("destination_name", $destination);
        $this->countryRepository
            ->expects(self::exactly(2))
            ->method("getCountryByCountryCode")
            ->withConsecutive(
                [$origin],
                [$destination]
            )
            ->willReturnOnConsecutiveCalls(
                $originCountry,
                $destinationCountry
            );
        $this->countryRepositoryFactory
            ->expects(self::exactly(2))
            ->method("getRepository")
            ->willReturn($this->countryRepository);
        $container->set(CountryRepositoryFactory::class, $this->countryRepositoryFactory);

        $routingService = $container->get(RoutingService::class);
        $this->assertNull(
            $routingService->getRoute($origin, $destination)
        );
    }

    public function test_mapRoute_returnsCorrectRoute_ifRouteImpossible_withoutIntermediaryNode()
    {
        $container = static::getContainer();
        $origin = "origin";
        $destination = "destination";
        $originCountry = new Country("origin_name", $origin, $destination);
        $destinationCountry = new Country("destination_name", $destination);
        $this->countryRepository
            ->expects(self::exactly(3))
            ->method("getCountryByCountryCode")
            ->withConsecutive(
                [$origin],
                [$destination],
                [$destination],
            )
            ->willReturnOnConsecutiveCalls(
                $originCountry,
                $destinationCountry,
                $destinationCountry,
            );
        $this->countryRepositoryFactory
            ->expects(self::exactly(3))
            ->method("getRepository")
            ->willReturn($this->countryRepository);
        $container->set(CountryRepositoryFactory::class, $this->countryRepositoryFactory);

        $routingService = $container->get(RoutingService::class);
        $this->assertEquals(
            new Countries([$originCountry, $destinationCountry]),
            $routingService->getRoute($origin, $destination)
        );
    }

    public function test_mapRoute_returnsCorrectRoute_ifRouteImpossible_withIntermediaryNode()
    {
        $container = static::getContainer();
        $origin = "origin";
        $destination = "destination";
        $intermediary = "intermediary";
        $originCountry = new Country("origin_name", $origin, $intermediary);
        $intermediaryCountry = new Country("intermediary_name", $intermediary, $destination);
        $destinationCountry = new Country("destination_name", $destination);
        $this->countryRepository
            ->expects(self::exactly(4))
            ->method("getCountryByCountryCode")
            ->withConsecutive(
                [$origin],
                [$destination],
                [$intermediary],
                [$destination],
            )
            ->willReturnOnConsecutiveCalls(
                $originCountry,
                $destinationCountry,
                $intermediaryCountry,
                $destinationCountry,
            );
        $this->countryRepositoryFactory
            ->expects(self::exactly(4))
            ->method("getRepository")
            ->willReturn($this->countryRepository);
        $container->set(CountryRepositoryFactory::class, $this->countryRepositoryFactory);

        $routingService = $container->get(RoutingService::class);
        $this->assertEquals(
            new Countries([$originCountry, $intermediaryCountry, $destinationCountry]),
            $routingService->getRoute($origin, $destination)
        );
    }

    public function test_mapRoute_returnsCorrectRoute_ifRouteImpossible_withIntermediaryNode_takesShortestRoute()
    {
        $container = static::getContainer();
        $origin = "origin";
        $destination = "destination";
        $intermediary = "intermediary";
        $intermediaryLong1 = "intermediaryLong1";
        $intermediaryLong2 = "intermediaryLong2";
        $originCountry = new Country("origin_name", $origin, $intermediary, $intermediaryLong1);
        $intermediaryCountry = new Country("intermediary_name", $intermediary, $destination);
        $intermediaryLong1Country = new Country("intermediary_long1_name", $intermediaryLong1, $intermediaryLong2);
        $intermediaryLong2Country = new Country("intermediary_long2_name", $intermediaryLong2, $destination);
        $destinationCountry = new Country("destination_name", $destination);
        $this->countryRepository
            ->expects(self::exactly(6))
            ->method("getCountryByCountryCode")
            ->withConsecutive(
                [$origin],
                [$destination],
                [$intermediary],
                [$intermediaryLong1],
                [$destination],
            )
            ->willReturnOnConsecutiveCalls(
                $originCountry,
                $destinationCountry,
                $intermediaryCountry,
                $intermediaryLong1Country,
                $destinationCountry,
            );
        $this->countryRepositoryFactory
            ->expects(self::exactly(6))
            ->method("getRepository")
            ->willReturn($this->countryRepository);
        $container->set(CountryRepositoryFactory::class, $this->countryRepositoryFactory);

        $routingService = $container->get(RoutingService::class);
        $this->assertEquals(
            new Countries([$originCountry, $intermediaryCountry, $destinationCountry]),
            $routingService->getRoute($origin, $destination)
        );
    }

    public function test_mapRoute_returnsCorrectRoute_ifRouteImpossible_withIntermediaryNode_doesntConsiderLongerRoutes()
    {
        $container = static::getContainer();
        $origin = "origin";
        $destination = "destination";
        $intermediary = "intermediary";
        $intermediaryLong1 = "intermediaryLong1";
        $intermediaryLong2 = "intermediaryLong2";
        $originCountry = new Country("origin_name", $origin, $intermediary, $intermediaryLong1);
        $intermediaryCountry = new Country("intermediary_name", $intermediary, $intermediaryLong1, $intermediaryLong2);
        $intermediaryLong1Country = new Country("intermediary_long1_name", $intermediaryLong1, $intermediaryLong2);
        $intermediaryLong2Country = new Country("intermediary_long2_name", $intermediaryLong2, $destination);
        $destinationCountry = new Country("destination_name", $destination);
        $this->countryRepository
            ->expects(self::exactly(11))
            ->method("getCountryByCountryCode")
            ->withConsecutive(
                [$origin],
                [$destination],
                [$intermediary],
                [$intermediaryLong1],
                [$intermediaryLong1],
                [$intermediaryLong2],
                [$intermediaryLong2],
                [$intermediaryLong2],
                [$destination],
                [$destination],
                [$destination],
            )
            ->willReturnOnConsecutiveCalls(
                $originCountry,
                $destinationCountry,
                $intermediaryCountry,
                $intermediaryLong1Country,
                $intermediaryLong1Country,
                $intermediaryLong2Country,
                $intermediaryLong2Country,
                $intermediaryLong2Country,
                $destinationCountry,
                $destinationCountry,
                $destinationCountry,
            );
        $this->countryRepositoryFactory
            ->expects(self::exactly(11))
            ->method("getRepository")
            ->willReturn($this->countryRepository);
        $container->set(CountryRepositoryFactory::class, $this->countryRepositoryFactory);

        $routingService = $container->get(RoutingService::class);
        $this->assertEquals(
            new Countries([$originCountry, $intermediaryCountry, $intermediaryLong2Country, $destinationCountry]),
            $routingService->getRoute($origin, $destination)
        );
    }
}
