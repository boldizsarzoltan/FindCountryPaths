<?php

namespace App\Entity;

class Country
{
    private string $name;
    private string $countryCode;
    /**
     * @var string[]
     */
    private array $borders;

    public function __construct(string $name, string $countryCode, string ...$borders)
    {
        $this->name = $name;
        $this->countryCode = $countryCode;
        $this->borders = $borders;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return array<string>
     */
    public function getBorders(): array
    {
        return $this->borders;
    }
}