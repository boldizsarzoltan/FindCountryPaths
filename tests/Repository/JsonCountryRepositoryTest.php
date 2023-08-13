<?php

namespace App\Tests\Repository;


use App\Entity\Countries;
use App\Entity\Country;
use App\Repository\DataContainer;
use App\Repository\JsonCountryRepository;
use App\Services\FileSystemService;
use App\Services\MappingService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class JsonCountryRepositoryTest extends KernelTestCase
{
    private FileSystemService|MockObject $fileSystemService;
    private MappingService|MockObject $mappingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileSystemService = $this->createMock(FileSystemService::class);
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
        $this->fileSystemService
            ->expects(self::once())
            ->method("readData")
            ->willReturn($dummyData);
        $this->mappingService
            ->expects(self::once())
            ->method("mapRawDataToCountries")
            ->with($dummyData)
            ->willReturn($countries);
        $container->set(FileSystemService::class, $this->fileSystemService);
        $container->set(MappingService::class, $this->mappingService);
        $this->assertEquals(
            $testCountry,
            $container->get(JsonCountryRepository::class)->getCountryByCountryCode("test_offset")
        );
    }

    public function test_worksAsIntendedIfInitialized_returnsNull()
    {
        $countries = new Countries();
        DataContainer::initialize($countries);
        $container = static::getContainer();
        $dummyData = ["dummyData"];
        $this->fileSystemService
            ->expects(self::once())
            ->method("readData")
            ->willReturn($dummyData);
        $this->mappingService
            ->expects(self::once())
            ->method("mapRawDataToCountries")
            ->with($dummyData)
            ->willReturn($countries);
        $container->set(FileSystemService::class, $this->fileSystemService);
        $container->set(MappingService::class, $this->mappingService);
        $this->assertNull(
            $container->get(JsonCountryRepository::class)->getCountryByCountryCode("test_offset")
        );
    }
}