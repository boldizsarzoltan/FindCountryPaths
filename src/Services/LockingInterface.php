<?php

namespace App\Services;

interface LockingInterface
{
    public function lock(): bool;

    public function unLock(): void;

    public function isLocked(): bool;
}