<?php

namespace App\Repository;

use App\Entity\Countries;
use App\Entity\Country;

class DataContainer
{
    private static self $instance;
    private function __construct(
        private readonly Countries $countries
    ) {
    }

    public static function initialize(Countries $data): void
    {
        self::$instance = new DataContainer($data);
    }

    public static function initialzed(): bool
    {
        return isset(self::$instance);
    }

    public static function getInstance(): DataContainer
    {
        if(!isset(self::$instance)) {
            throw new \RuntimeException("Data container not initialized");
        }
        return self::$instance;
    }

    /**
     * @return Countries<Country>
     */
    public function getCountries(): Countries
    {
        return $this->countries;
    }
}