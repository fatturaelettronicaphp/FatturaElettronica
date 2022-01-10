<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

interface ShipperInterface extends BillablePersonInterface
{
    public function getLicenseNumber(): ?string;

    public function setLicenseNumber(?string $licenseNumber = null): ShipperInterface;
}
