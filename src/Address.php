<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\AddressInterface;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;

class Address implements ArrayableInterface, AddressInterface
{
    use Arrayable;

    protected $street;
    protected $streetNumber;
    protected $zip;
    protected $city;
    protected $state;
    protected $countryCode;


    public function getStreet ()
    {
        return $this->street;
    }

    public function setStreet ($street): AddressInterface
    {
        $this->street = $street;
        return $this;
    }


    public function getStreetNumber ()
    {
        return $this->streetNumber;
    }

    public function setStreetNumber ($streetNumber): AddressInterface
    {
        $this->streetNumber = $streetNumber;
        return $this;
    }

    public function getZip ()
    {
        return $this->zip;
    }

    public function setZip ($zip): AddressInterface
    {
        $this->zip = $zip;
        return $this;
    }


    public function getCity ()
    {
        return $this->city;
    }


    public function setCity ($city): AddressInterface
    {
        $this->city = $city;
        return $this;
    }

    public function getState ()
    {
        return $this->state;
    }

    public function setState ($state): AddressInterface
    {
        $this->state = $state;
        return $this;
    }

    public function getCountryCode ()
    {
        return $this->countryCode;
    }

    public function setCountryCode ($countryCode): AddressInterface
    {
        $this->countryCode = $countryCode;
        return $this;
    }
}
