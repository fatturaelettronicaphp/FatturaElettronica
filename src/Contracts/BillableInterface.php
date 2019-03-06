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
}