<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\AddressInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\BillablePersonInterface;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class BillablePerson extends Billable implements BillablePersonInterface, ArrayableInterface
{
    use Arrayable;

    protected $title;
    protected $eori;
    protected $fiscalCode;

    /** @var AddressInterface */
    protected $address;

    /** @var AddressInterface */
    protected $foreignFixedAddress;

    public function getAddress(): ?AddressInterface
    {
        return $this->address;
    }

    public function setAddress(AddressInterface $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getForeignFixedAddress(): ?AddressInterface
    {
        return $this->foreignFixedAddress;
    }

    public function setForeignFixedAddress(AddressInterface $foreignFixedAddress): self
    {
        $this->foreignFixedAddress = $foreignFixedAddress;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getEori()
    {
        return $this->eori;
    }

    public function setEori($eori): self
    {
        $this->eori = $eori;

        return $this;
    }

    public function getFiscalCode()
    {
        return $this->fiscalCode;
    }

    public function setFiscalCode($fiscalCode): self
    {
        $this->fiscalCode = $fiscalCode;

        return $this;
    }
}
