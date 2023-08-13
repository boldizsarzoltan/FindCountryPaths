<?php

namespace App\Services;

use App\Entity\Countries;
use App\Entity\Country;
use App\Services\Exceptions\DuplicatedDataException;
use App\Services\Exceptions\MalFormedDataException;

class MappingService
{
    /**
     * @param array<array{
     *     name:array{
     *          official:string
     *      },
     *      cca3:string,
     *      borders:string[]
     * }|mixed> $datas
     * @return Countries<Country>
     */
    public function mapRawDataToCountries(array $datas): Countries
    {
        $countries = new Countries();
        foreach ($datas as $data) {
            try {
                $country = $this->mapRawDataToCountry($data);
                $countries->offsetSet(
                    $country->getCountryCode(),
                    $country
                );
            }
            catch (MalFormedDataException|DuplicatedDataException $dataException) {

            }
        }
        return $countries;
    }

    /**
     * @throws MalFormedDataException
     * @param array{
     *     name:array{
     *          official:string
     *      },
     *      cca3:string,
     *      borders:string[]
     * }|mixed $data
     */
    public function mapRawDataToCountry(array $data): Country
    {
        if(
            !isset($data["name"]) ||
            !isset($data["name"]["official"]) ||
            !is_string($data["name"]["official"]) ||
            !isset($data["cca3"]) ||
            !is_string($data["cca3"])
        ) {
            throw new MalFormedDataException();
        }

        return new Country(
            $data["name"]["official"],
            $data["cca3"],
            ...($data["borders"] ?? [])
        );
    }

    /**
     * @return string[]
     */
    public function mapRoute(Countries $route): array
    {
        $result = [];
        foreach ($route as $country) {
            /** @var Country $country $*/
            $result[] = $country->getCountryCode();
        }
        return $result;
    }
}