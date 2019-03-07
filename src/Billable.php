<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\AddressInterface;
use Weble\FatturaElettronica\Contracts\BillableInterface;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;

class Billable implements BillableInterface, ArrayableInterface
{
    use Arrayable;

    protected $title;
    protected $eori;
    protected $name;
    protected $surname;
    protected $organization;
    protected $countryCode;
    protected $vatNumber;
    protected $fiscalCode;

    /** @var \Weble\FatturaElettronica\Contracts\AddressInterface */
    protected $address;

    /** @var \Weble\FatturaElettronica\Contracts\AddressInterface */
    protected $foreignFixedAddress;

    public function getAddress (): ?AddressInterface
    {
        return $this->address;
    }

    public function setAddress (AddressInterface $address): BillableInterface
    {
        $this->address = $address;
        return $this;
    }

    public function getForeignFixedAddress (): ?AddressInterface
    {
        return $this->foreignFixedAddress;
    }

    public function setForeignFixedAddress (AddressInterface $foreignFixedAddress): BillableInterface
    {
        $this->foreignFixedAddress = $foreignFixedAddress;
        return $this;
    }

    public function getTitle ()
    {
        return $this->title;
    }

    public function setTitle ($title): BillableInterface
    {
        $this->title = $title;
        return $this;
    }

    public function getEori ()
    {
        return $this->eori;
    }

    public function setEori ($eori): BillableInterface
    {
        $this->eori = $eori;
        return $this;
    }

    public function getCountryCode ()
    {
        return $this->countryCode;
    }

    public function setCountryCode ($countryCode): BillableInterface
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    public function getName ()
    {
        return $this->name;
    }

    public function setName ($name): BillableInterface
    {
        $this->name = $name;
        return $this;
    }

    public function getVatNumber ()
    {
        return $this->vatNumber;
    }

    public function setVatNumber ($vatNumber): BillableInterface
    {
        $this->vatNumber = $vatNumber;
        return $this;
    }

    public function getSurname ()
    {
        return $this->surname;
    }

    public function setSurname ($surname): BillableInterface
    {
        $this->surname = $surname;
        return $this;
    }

    public function getOrganization ()
    {
        return $this->organization;
    }

    public function setOrganization ($organization): BillableInterface
    {
        $this->organization = $organization;
        return $this;
    }

    public function getFiscalCode ()
    {
        return $this->fiscalCode;
    }

    public function setFiscalCode ($fiscalCode): BillableInterface
    {
        $this->fiscalCode = $fiscalCode;
        return $this;
    }
}
