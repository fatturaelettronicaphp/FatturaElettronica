<?php

namespace Weble\FatturaElettronica\Contracts;

use Weble\FatturaElettronica\Billable;

interface BillableInterface
{
    public function getName ();

    public function setName ($name): BillableInterface;

    public function getVatNumber ();

    public function setVatNumber ($vatNumber): BillableInterface;

    public function getSurname ();

    public function setSurname ($surname): BillableInterface;

    public function getOrganization ();

    public function setOrganization ($organization): BillableInterface;

    public function getFiscalCode ();

    public function setFiscalCode ($fiscalCode): BillableInterface;

    public function setTitle ($title): BillableInterface;

    public function setEori ($eori): BillableInterface;

    public function setCountryCode ($countryCode): BillableInterface;

    public function getCountryCode ();

    public function getTitle ();

    public function getEori ();
}