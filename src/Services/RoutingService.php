<?php

namespace App\Services;

use App\Entity\Countries;
use App\Entity\Country;
use App\Model\TreeNode;
use App\Repository\CountryRepositoryFactory;

class RoutingService
{
    public function __construct(
        private readonly CountryRepositoryFactory $countryRepositoryFactory
    ) {
    }

    /**
     * @param string $origin
     * @param string $destination
     * @return Countries<Country>|null
     */
    public function getRoute(string $origin, string $destination): ?Countries
    {
        $startCountry = $this->countryRepositoryFactory
            ->getRepository()
            ->getCountryByCountryCode($origin);
        if(is_null($startCountry)) {
            return null;
        }
        $usedCountries = [$startCountry->getCountryCode()];
        if($origin == $destination) {
            return new Countries([$startCountry]);
        }
        $destinationCountry = $this->countryRepositoryFactory
            ->getRepository()
            ->getCountryByCountryCode($destination);
        if(is_null($destinationCountry)) {
            return null;
        }
        $currentNode = new TreeNode($startCountry);
        $candidateNodes = $this->parseCountries($currentNode, $usedCountries, $destinationCountry);
        do {
            $nextCandidateNodes = [];
            foreach($candidateNodes as $candidateNode) {
                /** @var TreeNode $candidateNode */
                if($candidateNode->country->getCountryCode() == $destinationCountry->getCountryCode()) {
                    return $this->traceBackRoute($candidateNode);
                }
                $usedCountries[] = $candidateNode->country->getCountryCode();
                $nextCandidateNodes = array_merge(
                    $nextCandidateNodes,
                    $this->parseCountries($candidateNode, $usedCountries, $destinationCountry)
                );
            }
            $candidateNodes = $nextCandidateNodes;
        }while(!empty($candidateNodes));

        return null;
    }

    private function traceBackRoute(TreeNode $currentNode): Countries
    {
        if(is_null($currentNode->parentNode)) {
            return new Countries([$currentNode->country]);
        }
        $countries = $this->traceBackRoute($currentNode->parentNode);
        $countries->append($currentNode->country);
        return $countries;
    }
    public function parseCountries(TreeNode $currentNode, array &$usedCountries, Country $destinationCountry): array
    {
        $nextNodes = [];
        foreach ($currentNode->country->getBorders() as $borderCountryCode) {
            $borderCountry = $this->countryRepositoryFactory
                ->getRepository()
                ->getCountryByCountryCode($borderCountryCode);
            if (is_null($borderCountry)) {
                continue;
            }
            if (in_array($borderCountry->getCountryCode(), $usedCountries)) {
                continue;
            }
            $nextNodes[] = new TreeNode($borderCountry, $currentNode);
        }
        return $nextNodes;
    }
}