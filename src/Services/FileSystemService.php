<?php

namespace App\Services;

use Symfony\Component\Filesystem\Filesystem;

class FileSystemService
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly string $filePath
    )
    {
    }

    public function fileExists(): bool
    {
        return $this->filesystem->exists($this->filePath);
    }

    public function canBeCreated(): bool
    {
        $parentDirectory = dirname($this->filePath);
        return $this->filesystem->exists($parentDirectory) && is_writable($parentDirectory);
    }

    public function writeData(array $data): void
    {
        if(!$this->filesystem->exists($this->filePath)) {
            $this->filesystem->touch($this->filePath);
        }
        $this->filesystem->appendToFile($this->filePath, json_encode($data));
    }

    public function readData(): array
    {
        if(!$this->filesystem->exists($this->filePath)) {
            return [];
        }
        $data = file_get_contents($this->filePath);
        if($data === false) {
            return [];
        }
        $extractedData = json_decode($data, true);
        if(empty($extractedData)) {
            return [];
        }
        return $extractedData;
    }
}