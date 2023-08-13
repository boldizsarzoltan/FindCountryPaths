<?php

namespace App\Tests\Repository;


use App\Entity\Countries;
use App\Entity\Country;
use App\Repository\DataContainer;
use App\Repository\HttpCountryRepository;
use App\Repository\LockingHttpCountryRepository;
use App\Services\FileSystemService;
use App\Services\HTTPService;
use App\Services\LockingService;
use App\Services\MappingService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LockingHttpCountryRepositoryTest extends KernelTestCase
{
    private HTTPService|MockObject $hTTPService;
    private MappingService|MockObject $mappingService;
    private MockObject|LockingService $lockingService;
    private FileSystemService|MockObject $fileSystemService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hTTPService = $this->createMock(HTTPService::class);
        $this->mappingService = $this->createMock(MappingService::class);
        $this->lockingService = $this->createMock(LockingService::class);
        $this->fileSystemService = $this->createMock(FileSystemService::class);
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
        $this->lockingService
            ->expects(self::once())
            ->method("lock");
        $this->lockingService
            ->expects(self::once())
            ->method("unLock");
        $this->fileSystemService
            ->expects(self::once())
            ->method("writeData")
            ->with($dummyData);
        $container->set(HTTPService::class, $this->hTTPService);
        $container->set(MappingService::class, $this->mappingService);
        $container->set(LockingService::class, $this->lockingService);
        $container->set(FileSystemService::class, $this->fileSystemService);
        $this->assertEquals(
            $testCountry,
            $container->get(LockingHttpCountryRepository::class)->getCountryByCountryCode("test_offset")
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
        $this->lockingService
            ->expects(self::once())
            ->method("lock");
        $this->lockingService
            ->expects(self::once())
            ->method("unLock");
        $this->fileSystemService
            ->expects(self::once())
            ->method("writeData")
            ->with($dummyData);
        $container->set(HTTPService::class, $this->hTTPService);
        $container->set(MappingService::class, $this->mappingService);
        $container->set(LockingService::class, $this->lockingService);
        $container->set(FileSystemService::class, $this->fileSystemService);
        $this->assertNull(
            $container->get(LockingHttpCountryRepository::class)->getCountryByCountryCode("test_offset")
        );
    }
}