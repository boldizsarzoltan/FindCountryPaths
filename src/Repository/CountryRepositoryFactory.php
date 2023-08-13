<?php

namespace App\Repository;

use App\Services\FileSystemService;
use App\Services\LockingInterface;

class CountryRepositoryFactory
{
    public function __construct(
        private readonly FileSystemService $fileSystemService,
        private readonly LockingInterface $lockingService,
        private readonly InMemoryCountryRepository $inMemoryCountryRepository,
        private readonly JsonCountryRepository $jsonCountryRepository,
        private readonly LockingHttpCountryRepository $lockingHttpCountryRepository,
        private readonly HttpCountryRepository $httpCountryRepository,
    ) {
    }

    public function getRepository(): CountryRepository
    {
        if(DataContainer::initialzed()) {
            return $this->inMemoryCountryRepository;
        }
        if($this->fileSystemService->fileExists()) {
            return $this->jsonCountryRepository;
        }
        if(!$this->lockingService->isLocked() && $this->fileSystemService->canBeCreated()) {
            return $this->lockingHttpCountryRepository;
        }
        return $this->httpCountryRepository;
    }
}