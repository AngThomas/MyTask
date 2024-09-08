<?php

namespace App\Interface;

interface BinFetcherInterface
{
    public function fetchCountryCode(string $bin): ?string;
}