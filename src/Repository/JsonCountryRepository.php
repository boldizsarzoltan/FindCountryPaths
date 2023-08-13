<?php

namespace App\Repository;

use App\Entity\Country;
use App\Services\FileSystemService;
use App\Services\MappingService;

class JsonCountryRepository extends InMemoryCountryRepository
{

    public function __construct(
        private readonly FileSystemService $fileSystemService,
        private readonly MappingService $mappingService
    ) {
    }

    public function getCountryByCountryCode(string $countryCode): ?Country
    {
        $this->readDataAndAddToMemory();
        return parent::getCountryByCountryCode($countryCode);
    }

    private function readDataAndAddToMemory(): void
    {
        $data = $this->fileSystemService->readData();
        DataContainer::initialize($this->mappingService->mapRawDataToCountries($data));
    }
}