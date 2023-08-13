<?php

namespace App\Repository;

use App\Entity\Country;
use App\Services\HTTPService;
use App\Services\MappingService;

class HttpCountryRepository extends InMemoryCountryRepository
{

    public function __construct(
        private readonly MappingService $mappingService,
        private readonly HTTPService $httpService
    ) {
    }

    public function getCountryByCountryCode(string $countryCode): ?Country
    {
        DataContainer::initialize($this->mappingService->mapRawDataToCountries($this->downloadData()));
        return parent::getCountryByCountryCode($countryCode);
    }

    protected function downloadData(): array
    {
        return $this->httpService->getData();
    }
}