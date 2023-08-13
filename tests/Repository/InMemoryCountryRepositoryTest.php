<?php

namespace App\Tests\Repository;


use App\Entity\Countries;
use App\Entity\Country;
use App\Repository\DataContainer;
use App\Repository\InMemoryCountryRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InMemoryCountryRepositoryTest extends KernelTestCase
{

    public function test_worksAsIntendedIfInitialized()
    {
        $testCountry = $this->createMock(Country::class);
        $countries = new Countries();
        $countries->offsetSet("test_offset", $testCountry);
        DataContainer::initialize($countries);
        $container = static::getContainer();
        $this->assertEquals(
            $testCountry,
            $container->get(InMemoryCountryRepository::class)->getCountryByCountryCode("test_offset")
        );
    }

    public function test_worksAsIntendedIfInitialized_returnsNull()
    {
        $countries = new Countries();
        DataContainer::initialize($countries);
        $container = static::getContainer();
        $this->assertNull(
            $container->get(InMemoryCountryRepository::class)->getCountryByCountryCode("test_offset")
        );
    }
}