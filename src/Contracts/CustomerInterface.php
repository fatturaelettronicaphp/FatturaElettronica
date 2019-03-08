<?php

namespace Weble\FatturaElettronica\Contracts;

use DateTime;
use Weble\FatturaElettronica\Enums\AssociateType;
use Weble\FatturaElettronica\Enums\SettlementType;
use Weble\FatturaElettronica\Enums\TaxRegime;

interface CustomerInterface extends BillablePersonInterface
{
    public function getRepresentative ();

    public function setRepresentative (?RepresentativeInterface $representative);
}