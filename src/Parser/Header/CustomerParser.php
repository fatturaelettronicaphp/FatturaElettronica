<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Header;


use FatturaElettronicaPhp\FatturaElettronica\Customer;
use FatturaElettronicaPhp\FatturaElettronica\Representative;

class CustomerParser extends AbstractHeaderParser
{
    protected function performParsing ()
    {
        $customer = new Customer();

        $customerName = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica/Nome');
        $customer->setName($customerName);

        $customerSurname = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica/Cognome');
        $customer->setSurname($customerSurname);

        $customerOrganization = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica/Denominazione');
        $customer->setOrganization($customerOrganization);

        $customerFiscalCode = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/CodiceFiscale');
        $customer->setFiscalCode($customerFiscalCode);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/IdFiscaleIVA/IdPaese');
        $customer->setCountryCode($value);

        $customerVatNumber = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/IdFiscaleIVA/IdCodice');
        if ($customerVatNumber === null) {
            $customerVatNumber = '';
        }
        $customer->setVatNumber($customerVatNumber);

        $addressValue = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/Sede', false);
        if ($addressValue !== null) {
            $addressValue = array_shift($addressValue);
        }

        if ($addressValue !== null) {
            $address = $this->extractAddressInformationFrom($addressValue);
            $customer->setAddress($address);
        }

        $addressValue = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/StabileOrganizzazione', false);
        if ($addressValue !== null) {
            $addressValue = array_shift($addressValue);
        }

        if ($addressValue !== null) {
            $address = $this->extractAddressInformationFrom($addressValue);
            $customer->setForeignFixedAddress($address);
        }

        $representativeValue = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/RappresentanteFiscale', false);
        if ($representativeValue !== null) {
            $representativeValue = array_shift($representativeValue);
        }

        if ($representativeValue !== null) {
            $representative = new Representative();
            $this->extractBillableInformationsFrom($representativeValue, $representative);
            $customer->setRepresentative($representative);
        }

        $this->document->setCustomer($customer);

        return $this->document;
    }
}
