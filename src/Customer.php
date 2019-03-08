<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\CustomerInterface;
use Weble\FatturaElettronica\Contracts\RepresentativeInterface;

class Customer extends BillablePerson implements CustomerInterface
{
    /** @var \Weble\FatturaElettronica\Contracts\RepresentativeInterface */
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
