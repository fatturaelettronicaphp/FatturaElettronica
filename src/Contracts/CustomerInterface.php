<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Enums\AssociateType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\WoundUpType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\TaxRegime;

interface CustomerInterface extends BillablePersonInterface
{
    public function getRepresentative (): ?RepresentativeInterface;

    public function setRepresentative (?RepresentativeInterface $representative);
}