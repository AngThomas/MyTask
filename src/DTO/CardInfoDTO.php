<?php

namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;

class CardInfoDTO
{
    public function __construct(
        #[Serializer\Type('string'), Serializer\Since('0.1')]
        private string $scheme,
        #[Serializer\Type('string'), Serializer\Since('0.1')]
        private string $type,
        #[Serializer\Type('string'), Serializer\Since('0.1')]
        private string $brand,
        #[Serializer\Type('App\DTO\CountryDTO'), Serializer\Since('0.1')]
        private CountryDTO $country,
        #[Serializer\Type('App\DTO\BankDTO'), Serializer\Since('0.1')]
        private BankDTO $bank

    )
    {
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function setScheme(string $scheme): self
    {
        $this->scheme = $scheme;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getCountry(): CountryDTO
    {
        return $this->country;
    }

    public function setCountry(CountryDTO $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getBank(): BankDTO
    {
        return $this->bank;
    }

    public function setBank(BankDTO $bank): self
    {
        $this->bank = $bank;

        return $this;
    }


}