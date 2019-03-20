<?php

namespace Weble\FatturaElettronica\Contracts;

use DateTime;
use Weble\FatturaElettronica\Enums\AssociateType;
use Weble\FatturaElettronica\Enums\WoundUpType;
use Weble\FatturaElettronica\Enums\TaxRegime;

interface CustomerInterface extends BillablePersonInterface
{
    public function getRepresentative (): ?RepresentativeInterface;

    public function setRepresentative (?RepresentativeInterface $representative);
}