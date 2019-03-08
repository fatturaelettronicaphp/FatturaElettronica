<?php

namespace Weble\FatturaElettronica\Contracts;

interface BillableInterface
{
    public function getName ();

    public function setName ($name);

    public function getVatNumber ();

    public function setVatNumber ($vatNumber);

    public function getSurname ();

    public function setSurname ($surname);

    public function getOrganization ();

    public function setOrganization ($organization);

    public function setCountryCode ($countryCode);

    public function getCountryCode ();

}