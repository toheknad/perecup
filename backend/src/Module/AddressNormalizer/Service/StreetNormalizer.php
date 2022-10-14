<?php

namespace App\Module\AddressNormalizer\Service;

class StreetNormalizer
{
    private StreetNormalizerInterface $driver;

    public function setDriver(StreetNormalizerInterface $driver): void
    {
        $this->driver = $driver;
    }

    public function normalize(string $address)
    {
        return $this->driver->normalize($address);
    }
}

