<?php

namespace App\Tests\Entity;


use App\Entity\Country;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CountryTest extends KernelTestCase
{
    public function test_worksAsIntended()
    {
        $name = "name";
        $countryCode = "countryCode";
        $borders = ["border1", "border2"];
        $country = new Country($name, $countryCode, "border1", "border2");
        $this->assertEquals($name, $country->getName());
        $this->assertEquals($countryCode, $country->getCountryCode());
        $this->assertEquals($borders, $country->getBorders());
    }
}