<?php

namespace App\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlRoot(name: 'transaction')]
class Transaction
{

    public function __construct(
        #[Serializer\Since('0.1'), Serializer\Type(name: 'string')]
        private string $bin,
        #[Serializer\Since('0.1'), Serializer\Type(name: 'float')]
        private float $amount,
        #[Serializer\Since('0.1'), Serializer\Type(name: 'string')]
        private string $currency
    )
    {
    }

    public function getBin(): string
    {
        return $this->bin;
    }

    public function setBin(string $bin): self
    {
        $this->bin = $bin;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

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
