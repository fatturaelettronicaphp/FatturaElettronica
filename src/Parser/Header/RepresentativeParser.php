<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Header;

use FatturaElettronicaPhp\FatturaElettronica\BillablePerson;

class RepresentativeParser extends AbstractHeaderParser
{
    protected function performParsing()
    {
        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale', false);
        if ($value === null) {
            return $this->document;
        }

        $value = array_shift($value);
        if ($value === null) {
            return $this->document;
        }

        $intermediary = new BillablePerson();

        $documentName = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/Anagrafica/Nome');
        $intermediary->setName($documentName);

        $documentSurname = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/Anagrafica/Cognome');
        $intermediary->setSurname($documentSurname);

        $documentOrganization = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/Anagrafica/Denominazione');
        $intermediary->setOrganization($documentOrganization);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/Anagrafica/Titolo');
        $intermediary->setTitle($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/CodiceFiscale');
        $intermediary->setFiscalCode($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/IdFiscaleIVA/IdCodice');
        $intermediary->setVatNumber($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/IdFiscaleIVA/IdPaese');
        $intermediary->setCountryCode($value);

        return $this->document;
    }
}
