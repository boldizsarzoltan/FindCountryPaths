<?php

namespace App\Tests\Services;


use App\Entity\Countries;
use App\Entity\Country;
use App\Services\Exceptions\MalFormedDataException;
use App\Services\MappingService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class MappingServiceTest extends KernelTestCase
{
    public function incorrectDataProdvider()
    {
        return[
            "missing name" => [[]],
            "missing name official" => [["name" => []]],
            "not string name official" => [["name" => ["official" => []]]],
            "missing cca3" => [["name" => ["official" => "official"]]],
            "not string cca3" => [["name" => ["official" => "official"], "cca3" => []]],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function test_mapRoute()
    {
        $container = static::getContainer();
        $country1 = new Country("name1", "country_code_1");
        $country2 = new Country("name2", "country_code_2");
        $mappingService = $container->get(MappingService::class);
        $this->assertEquals(
            ["country_code_1", "country_code_2"],
            $mappingService->mapRoute(new Countries([$country1, $country2]))
        );
    }

    /**
     * @dataProvider incorrectDataProdvider
     */
    public function test_mapRawDataToCountry_throwsException(array $data)
    {
        $container = static::getContainer();
        $this->expectException(MalFormedDataException::class);
        $container->get(MappingService::class)->mapRawDataToCountry($data);
    }

    public function test_mapRawDataToCountry_worksCorrectly()
    {
        $data =
            [
                "name" => [
                    "official" => "official"
                ],
                "cca3" => "cca3",
                "borders" => ["border1", "border2"]
            ];
        $container = static::getContainer();
        $this->assertEquals(
            new Country("official", "cca3", "border1", "border2"),
            $container->get(MappingService::class)->mapRawDataToCountry($data)
        );
    }

    public function test_mapRawDataToCountries_worksCorrectly()
    {
        $data =[
            [
                "name" => [
                    "official" => "official"
                ],
                "cca3" => "cca3",
                "borders" => ["border1", "border2"]
            ],
            [
                "name" => [
                    "official" => "official2"
                ],
                "cca3" => "cca4",
                "borders" => ["border3", "border4"]
            ],
        ];
        $container = static::getContainer();
        $this->assertEquals(
            new Countries(
                [
                    "cca3" => new Country("official", "cca3", "border1", "border2"),
                    "cca4" => new Country("official2", "cca4", "border3", "border4"),
                ]
            ),
            $container->get(MappingService::class)->mapRawDataToCountries($data)
        );
    }
}
