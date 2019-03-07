<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\RepresentativeInterface;

class Customer extends Billable
{
    /** @var \Weble\FatturaElettronica\Contracts\RepresentativeInterface */
    protected $representative;

    public function getRepresentative (): RepresentativeInterface
    {
        return $this->representative;
    }

    public function setRepresentative (RepresentativeInterface $representative): Customer
    {
        $this->representative = $representative;
        return $this;
    }


}
