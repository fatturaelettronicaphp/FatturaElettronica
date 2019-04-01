<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

interface CustomerInterface extends BillablePersonInterface
{
    public function getRepresentative (): ?RepresentativeInterface;

    public function setRepresentative (?RepresentativeInterface $representative);
}