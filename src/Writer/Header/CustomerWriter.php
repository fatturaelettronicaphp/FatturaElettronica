<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer\Header;

use FatturaElettronicaPhp\FatturaElettronica\Customer;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\SimpleXmlExtended;

class CustomerWriter extends AbstractHeaderWriter
{
    protected function performWrite()
    {
        $cessionarioCommittente = $this->xml->addChild('CessionarioCommittente');
        $datiAnagrafici = $cessionarioCommittente->addChild('DatiAnagrafici');

        /** @var Customer $customer */
        $customer = $this->document->getCustomer();

        $idPaese = $customer->getCountryCode();
        $codiceFiscale = $customer->getFiscalCode();
        $vatNumber = $customer->getVatNumber();

        $fiscalData = $this->calculateFiscalData($idPaese, $codiceFiscale, $vatNumber);

        if (! empty($vatNumber)) {
            $idFiscaleIva = $datiAnagrafici->addChild('IdFiscaleIVA');
            $idFiscaleIva->addChild('IdPaese', $idPaese);
            $idFiscaleIva->addChild('IdCodice', SimpleXmlExtended::sanitizeText($fiscalData['idCodice']));
        }

        if (! empty($fiscalData['codiceFiscale'])) {
            $datiAnagrafici->addChild('CodiceFiscale', SimpleXmlExtended::sanitizeText($fiscalData['codiceFiscale']));
        }

        $anagrafica = $datiAnagrafici->addChild('Anagrafica');

        if (! empty($customer->getOrganization())) {
            $anagrafica->addChild('Denominazione', SimpleXmlExtended::sanitizeText($customer->getOrganization()));
        } else {
            if (! empty($customer->getName())) {
                $anagrafica->addChild('Nome', SimpleXmlExtended::sanitizeText($customer->getName()));
            }

            if (! empty($customer->getSurname())) {
                $anagrafica->addChild('Cognome', SimpleXmlExtended::sanitizeText($customer->getSurname()));
            }
        }

        $address = $customer->getAddress();

        if ($address) {
            $sede = $cessionarioCommittente->addChild('Sede');
            $sede->addChild('Indirizzo', SimpleXmlExtended::sanitizeText($address->getStreet()));

            if (! empty($address->getStreetNumber())) {
                $sede->addChild('NumeroCivico', SimpleXmlExtended::sanitizeText($address->getStreetNumber()));
            }

            $sanitizedCap = preg_replace("/[^0-9]/", '', $address->getZip());
            $sanitizedCap = substr($sanitizedCap, 0, 5);
            $sanitizedCap = str_pad($sanitizedCap, 5, '0', STR_PAD_LEFT);

            $sede->addChild('CAP', $sanitizedCap);
            $sede->addChild('Comune', SimpleXmlExtended::sanitizeText($address->getCity()));
            if (! empty($address->getState()) and ($address->getCountryCode() == 'IT')) {
                $sede->addChild('Provincia', strtoupper(SimpleXmlExtended::sanitizeText($address->getState())));
            }

            $sede->addChild('Nazione', $address->getCountryCode());
        }

        $address = $customer->getForeignFixedAddress();
        if ($address) {
            $stabileOrganizzazione = $cessionarioCommittente->addChild('StabileOrganizzazione');

            $stabileOrganizzazione->addChild('Indirizzo', SimpleXmlExtended::sanitizeText($address->getStreet()));

            if (! empty($address->getStreetNumber())) {
                $stabileOrganizzazione->addChild('NumeroCivico', SimpleXmlExtended::sanitizeText($address->getStreetNumber()));
            }

            $sanitizedCap = preg_replace("/[^0-9]/", '', $address->getZip());
            $sanitizedCap = substr($sanitizedCap, 0, 5);
            $sanitizedCap = str_pad($sanitizedCap, 5, '0', STR_PAD_LEFT);

            $stabileOrganizzazione->addChild('CAP', $sanitizedCap);

            $stabileOrganizzazione->addChild('Comune', SimpleXmlExtended::sanitizeText($address->getCity()));

            if (! empty($address->getState()) and ($address->getCountryCode() == 'IT')) {
                $stabileOrganizzazione->addChild('Provincia', strtoupper(SimpleXmlExtended::sanitizeText($address->getState())));
            }

            $stabileOrganizzazione->addChild('Nazione', $address->getCountryCode());
        }

        $personAgent = $customer->getRepresentative();

        if ($personAgent) {
            $rappresentanteFiscale = $cessionarioCommittente->addChild('RappresentanteFiscale');

            $idFiscaleIva = $rappresentanteFiscale->addChild('IdFiscaleIVA');
            $idFiscaleIva->addChild('IdPaese', $personAgent->getCountryCode());
            $idFiscaleIva->addChild('IdCodice', SimpleXmlExtended::sanitizeText($personAgent->getVatNumber()));

            if (! empty($personAgent->getOrganization())) {
                $rappresentanteFiscale->addChild('Denominazione', SimpleXmlExtended::sanitizeText($personAgent->getOrganization()));
            } else {
                if (! empty($personAgent->getName())) {
                    $rappresentanteFiscale->addChild('Nome', SimpleXmlExtended::sanitizeText($personAgent->getName()));
                }

                if (! empty($personAgent->getSurname())) {
                    $rappresentanteFiscale->addChild('Cognome', SimpleXmlExtended::sanitizeText($personAgent->getSurname()));
                }
            }
        }

        return $this;
    }
}
