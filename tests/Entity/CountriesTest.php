<?php

namespace App\Tests\Entity;


use App\Entity\Countries;
use App\Entity\Country;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CountriesTest extends KernelTestCase
{
    public function test_worksAsIntended()
    {
        $country1 = $this->createMock(Country::class);
        $country2 = $this->createMock(Country::class);
        $this->assertEquals(
            [$country1, $country2],
            (new Countries([$country1, $country2]))->getArrayCopy()
        );
        $countries = new Countries();
        $countries->append($country1);
        $this->assertEquals(
            [$country1],
            $countries->getArrayCopy()
        );
        $countries->append($country2);
        $this->assertEquals(
            [$country1, $country2],
            $countries->getArrayCopy()
        );
    }

    public function test_worksAsIntended_withOffset()
    {
        $country1 = $this->createMock(Country::class);
        $country2 = $this->createMock(Country::class);
        $countries = new Countries();
        $countries->offsetSet("country_code_1", $country1);
        $this->assertEquals(
            ["country_code_1" => $country1],
            $countries->getArrayCopy()
        );
        $countries->offsetSet("country_code_2", $country2);
        $this->assertEquals(
            ["country_code_1" => $country1, "country_code_2" => $country2],
            $countries->getArrayCopy()
        );
    }
}