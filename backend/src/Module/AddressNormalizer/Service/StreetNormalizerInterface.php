<?php

namespace App\Module\AddressNormalizer\Service;

interface StreetNormalizerInterface
{
    public function normalize(string $address);
}