<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Header;

use FatturaElettronicaPhp\FatturaElettronica\BillablePerson;

class IntermediaryParser extends AbstractHeaderParser
{
    protected function performParsing()
    {
        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente', false);
        if ($value === null) {
            return $this->document;
        }

        $value = array_shift($value);
        if ($value === null) {
            return $this->document;
        }

        $intermediary = new BillablePerson();

        $documentName = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/Anagrafica/Nome');
        $intermediary->setName($documentName);

        $documentSurname = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/Anagrafica/Cognome');
        $intermediary->setSurname($documentSurname);

        $documentOrganization = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/Anagrafica/Denominazione');
        $intermediary->setOrganization($documentOrganization);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/Anagrafica/Titolo');
        $intermediary->setTitle($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/CodiceFiscale');
        $intermediary->setFiscalCode($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/IdFiscaleIVA/IdCodice');
        $intermediary->setVatNumber($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/IdFiscaleIVA/IdPaese');
        $intermediary->setCountryCode($value);

        return $this->document;
    }
}
