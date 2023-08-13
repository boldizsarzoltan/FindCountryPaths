<?php

namespace App\Tests\Repository;


use App\Entity\Countries;
use App\Repository\DataContainer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DataContainerTest extends KernelTestCase
{
    public function test_throwsExceptionIfNotInitialized()
    {
        $this->expectException(\RuntimeException::class);
        DataContainer::getInstance();
    }

    public function test_worksAsIntended()
    {
        $countries = $this->createMock(Countries::class);
        DataContainer::initialize($countries);
        $this->assertEquals($countries, DataContainer::getInstance()->getCountries());
    }
}