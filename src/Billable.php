<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\BillableInterface;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class Billable implements ArrayableInterface, BillableInterface
{
    use Arrayable;

    protected $name;
    protected $surname;
    protected $organization;
    protected $countryCode;
    protected $vatNumber;

    public function getCountryCode()
    {
        return $this->countryCode;
    }

    public function setCountryCode($countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getVatNumber()
    {
        return $this->vatNumber;
    }

    public function setVatNumber($vatNumber): self
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function setSurname($surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getOrganization()
    {
        return $this->organization;
    }

    public function setOrganization($organization): self
    {
        $this->organization = $organization;

        return $this;
    }
}
