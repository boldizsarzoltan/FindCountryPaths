<?php

namespace App\Repository;

use App\Entity\Country;

interface CountryRepository
{
    public function getCountryByCountryCode(string $countryCode): ?Country;
}