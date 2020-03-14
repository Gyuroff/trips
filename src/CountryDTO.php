<?php


namespace App;


use App\Entity\Country;
use Symfony\Component\Validator\Constraints as Assert;

class CountryDTO
{
    /**
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @Assert\NotBlank()
     */
    public $alpha3Code;

    public function toEntity(): Country
    {
        $country = new Country();
        $country->setName($this->name);
        $country->setAlpha3Code($this->alpha3Code);

        return $country;
    }
}