<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\BillableInterface;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;

class Billable implements BillableInterface, ArrayableInterface
{
    use Arrayable;

    public $name;
    public $vatNumber;
    public $surname;
    public $organization;
    public $fiscalCode;

    public function getName ()
    {
        return $this->name;
    }

    public function setName ($name): Billable
    {
        $this->name = $name;
        return $this;
    }

    public function getVatNumber ()
    {
        return $this->vatNumber;
    }

    public function setVatNumber ($vatNumber): Billable
    {
        $this->vatNumber = $vatNumber;
        return $this;
    }

    public function getSurname ()
    {
        return $this->surname;
    }

    public function setSurname ($surname): Billable
    {
        $this->surname = $surname;
        return $this;
    }

    public function getOrganization ()
    {
        return $this->organization;
    }

    public function setOrganization ($organization): Billable
    {
        $this->organization = $organization;
        return $this;
    }

    public function getFiscalCode ()
    {
        return $this->fiscalCode;
    }

    public function setFiscalCode ($fiscalCode): Billable
    {
        $this->fiscalCode = $fiscalCode;
        return $this;
    }
}
