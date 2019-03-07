<?php

namespace Weble\FatturaElettronica\Contracts;

use Weble\FatturaElettronica\Billable;

interface BillableInterface
{
    public function getName ();

    public function setName ($name): Billable;

    public function getVatNumber ();

    public function setVatNumber ($vatNumber): Billable;

    public function getSurname ();

    public function setSurname ($surname): Billable;

    public function getOrganization ();

    public function setOrganization ($organization): Billable;

    public function getFiscalCode ();

    public function setFiscalCode ($fiscalCode): Billable;

    public function setTitle ($title): BillableInterface;

    public function setEori ($eori): BillableInterface;

    public function setCountryCode ($countryCode): BillableInterface;

    public function getCountryCode ();

    public function getTitle ();

    public function getEori ();
}