<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\CustomerInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\RepresentativeInterface;

class Customer extends BillablePerson implements CustomerInterface
{
    /** @var RepresentativeInterface */
    protected $representative;

    public function getRepresentative (): ?RepresentativeInterface
    {
        return $this->representative;
    }

    public function setRepresentative (?RepresentativeInterface $representative): self
    {
        $this->representative = $representative;
        return $this;
    }


}
