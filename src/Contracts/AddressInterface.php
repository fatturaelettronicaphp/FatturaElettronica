<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

interface AddressInterface
{
    public function getStreet();

    public function setStreet($street): AddressInterface;

    public function getStreetNumber();

    public function setStreetNumber($streetNumber): AddressInterface;

    public function getZip();

    public function setZip($zip): AddressInterface;

    public function getCity();

    public function setCity($city): AddressInterface;

    public function getState();

    public function setState($state): AddressInterface;

    public function getCountryCode();

    public function setCountryCode($countryCode): AddressInterface;
}
