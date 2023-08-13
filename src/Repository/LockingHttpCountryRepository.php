<?php

namespace App\Repository;

use App\Services\FileSystemService;
use App\Services\HTTPService;
use App\Services\LockingService;
use App\Services\MappingService;

class LockingHttpCountryRepository extends HttpCountryRepository
{
    public function __construct(
        private readonly FileSystemService $fileSystemService,
        private readonly LockingService $lockingService,
        private readonly MappingService $mappingService,
        private readonly HTTPService $httpService
    ) {
        parent::__construct($this->mappingService, $this->httpService);
    }

    protected function downloadData(): array
    {
        $this->lockingService->lock();
        $data = parent::downloadData();
        $this->fileSystemService->writeData($data);
        $this->lockingService->unLock();
        return $data;
    }
}