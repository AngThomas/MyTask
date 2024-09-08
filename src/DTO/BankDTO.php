<?php

namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;

class BankDTO
{
    #[Serializer\Type('string'), Serializer\Since('0.1')]
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
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

}