<?php

namespace App\Tests\Repository;


use App\Entity\Countries;
use App\Entity\Country;
use App\Repository\DataContainer;
use App\Repository\HttpCountryRepository;
use App\Services\HTTPService;
use App\Services\MappingService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HttpCountryRepositoryTest extends KernelTestCase
{
    private HTTPService|MockObject $hTTPService;
    private MappingService|MockObject $mappingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hTTPService = $this->createMock(HTTPService::class);
        $this->mappingService = $this->createMock(MappingService::class);
        self::bootKernel();
    }

    public function test_worksAsIntendedIfInitialized()
    {
        $testCountry = $this->createMock(Country::class);
        $countries = new Countries();
        $countries->offsetSet("test_offset", $testCountry);
        DataContainer::initialize($countries);
        $dummyData = ["dummyData"];
        $container = static::getContainer();
        $this->hTTPService
            ->expects(self::once())
            ->method("getData")
            ->willReturn($dummyData);
        $this->mappingService
            ->expects(self::once())
            ->method("mapRawDataToCountries")
            ->with($dummyData)
            ->willReturn($countries);
        $container->set(HTTPService::class, $this->hTTPService);
        $container->set(MappingService::class, $this->mappingService);
        $this->assertEquals(
            $testCountry,
            $container->get(HttpCountryRepository::class)->getCountryByCountryCode("test_offset")
        );
    }

    public function test_worksAsIntendedIfInitialized_returnsNull()
    {
        $countries = new Countries();
        DataContainer::initialize($countries);
        $dummyData = ["dummyData"];
        $container = static::getContainer();
        $this->hTTPService
            ->expects(self::once())
            ->method("getData")
            ->willReturn($dummyData);
        $this->mappingService
            ->expects(self::once())
            ->method("mapRawDataToCountries")
            ->with($dummyData)
            ->willReturn($countries);
        $container->set(HTTPService::class, $this->hTTPService);
        $container->set(MappingService::class, $this->mappingService);
        $this->assertNull(
            $container->get(HttpCountryRepository::class)->getCountryByCountryCode("test_offset")
        );
    }
}