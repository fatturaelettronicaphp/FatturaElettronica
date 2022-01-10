<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\ShipperInterface;

class Shipper extends BillablePerson implements ShipperInterface
{
    /** @var string|null */
    protected $licenseNumber;

    public function getLicenseNumber(): ?string
    {
        return $this->licenseNumber;
    }

    public function setLicenseNumber(?string $licenseNumber = null): ShipperInterface
    {
        $this->licenseNumber = $licenseNumber;

        return $this;
    }
}
