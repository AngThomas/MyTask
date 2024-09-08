<?php

namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;

class CountryDTO  
{
    public function __construct(
        #[Serializer\Type('string'), Serializer\Since('0.1')]
        private string $alpha2,
        #[Serializer\Type('string'), Serializer\Since('0.1')]
        private string $name,
        #[Serializer\Type('string'), Serializer\Since('0.1')]
        private string $currency
    )
    {
    }

    public function getAlpha2(): string
    {
        return $this->alpha2;
    }

    public function setAlpha2(string $alpha2): self
    {
        $this->alpha2 = $alpha2;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    
}