<?php

namespace App\Services;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\SharedLockInterface;
use Symfony\Component\Lock\Store\SemaphoreStore;

class LockingService implements LockingInterface
{
    private const JSON_FILE_LOCK = "json_file_lock";
    private LockInterface|SharedLockInterface $lock;

    public function __construct(
        private readonly SemaphoreStore $semaphoreStore,
    )
    {
        $this->lock = (new LockFactory($this->semaphoreStore))->createLock(
            self::JSON_FILE_LOCK
        );
    }

    public function lock(): bool
    {
        return $this->lock->acquire(true);
    }

    public function unLock(): void
    {
        $this->lock->release();
    }

    public function isLocked(): bool
    {
        return $this->lock->isAcquired();
    }
}