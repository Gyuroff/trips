<?php


namespace App;

use App\Entity\Country;

class CountryDTOToCountry
{
    public function transform(CountryDTO $countryDTO): Country
    {
        $country = new Country();
        $country->setName($countryDTO->name);
        $country->setAlpha3Code($countryDTO->alpha3Code);

        return $country;
    }
}