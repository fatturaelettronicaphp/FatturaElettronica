<?php

namespace Weble\FatturaElettronica\Contracts;

interface RepresentativeInterface
{
    public function getCountryCode ();

    public function setCountryCode ($countryCode): RepresentativeInterface;

    public function getName ();

    public function setName ($name): RepresentativeInterface;

    public function getVatNumber ();

    public function setVatNumber ($vatNumber): RepresentativeInterface;

    public function getSurname ();

    public function setSurname ($surname): RepresentativeInterface;

    public function getOrganization ();

    public function setOrganization ($organization): RepresentativeInterface;
}