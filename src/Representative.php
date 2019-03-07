<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\AddressInterface;
use Weble\FatturaElettronica\Contracts\BillableInterface;
use Weble\FatturaElettronica\Contracts\RepresentativeInterface;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;

class Representative implements ArrayableInterface, RepresentativeInterface
{
    use Arrayable;

    protected $name;
    protected $surname;
    protected $organization;
    protected $countryCode;
    protected $vatNumber;

    public function getCountryCode ()
    {
        return $this->countryCode;
    }

    public function setCountryCode ($countryCode): RepresentativeInterface
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    public function getName ()
    {
        return $this->name;
    }

    public function setName ($name): RepresentativeInterface
    {
        $this->name = $name;
        return $this;
    }

    public function getVatNumber ()
    {
        return $this->vatNumber;
    }

    public function setVatNumber ($vatNumber): RepresentativeInterface
    {
        $this->vatNumber = $vatNumber;
        return $this;
    }

    public function getSurname ()
    {
        return $this->surname;
    }

    public function setSurname ($surname): RepresentativeInterface
    {
        $this->surname = $surname;
        return $this;
    }

    public function getOrganization ()
    {
        return $this->organization;
    }

    public function setOrganization ($organization): RepresentativeInterface
    {
        $this->organization = $organization;
        return $this;
    }
}
