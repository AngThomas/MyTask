<?php

namespace App\Interface;

interface BinFetcherInterface
{
    public function fetchBinData(string $bin): string;
}