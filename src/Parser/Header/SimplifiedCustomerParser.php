<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Header;

use FatturaElettronicaPhp\FatturaElettronica\Customer;
use FatturaElettronicaPhp\FatturaElettronica\Representative;

class SimplifiedCustomerParser extends AbstractHeaderParser
{
    protected function performParsing()
    {
        if (! $this->document->isSimplified()) {
            return $this->document;
        }

        $customer = new Customer();

        $prefix       = '//FatturaElettronicaHeader/CessionarioCommittente/';
        $customerName = $this->extractValueFromXml($prefix . 'AltriDatiIdentificativi/Nome');
        $customer->setName($customerName);

        $customerSurname = $this->extractValueFromXml($prefix . 'AltriDatiIdentificativi/Cognome');
        $customer->setSurname($customerSurname);

        $customerOrganization = $this->extractValueFromXml($prefix . 'AltriDatiIdentificativi/Denominazione');
        $customer->setOrganization($customerOrganization);

        $customerFiscalCode = $this->extractValueFromXml($prefix . 'IdentificativiFiscali/CodiceFiscale');
        $customer->setFiscalCode($customerFiscalCode);

        $value = $this->extractValueFromXml($prefix . 'IdentificativiFiscali/IdFiscaleIVA/IdPaese');
        $customer->setCountryCode($value);

        $customerVatNumber = $this->extractValueFromXml($prefix . 'IdentificativiFiscali/IdFiscaleIVA/IdCodice');
        if ($customerVatNumber === null) {
            $customerVatNumber = '';
        }
        $customer->setVatNumber($customerVatNumber);

        $addressValue = $this->extractValueFromXml($prefix . 'AltriDatiIdentificativi/Sede', false);
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
