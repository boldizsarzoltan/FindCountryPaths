<?php

namespace App\Repository;

use App\Entity\Country;

class InMemoryCountryRepository implements CountryRepository
{
    public function getCountryByCountryCode(string $countryCode): ?Country
    {
        $countries = DataContainer::getInstance()->getCountries();
        if(!$countries->offsetExists($countryCode)) {
            return null;
        }
        return $countries->offsetGet($countryCode);
    }
}