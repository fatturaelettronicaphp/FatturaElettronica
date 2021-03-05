<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer\Header;

use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidDocument;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\SimpleXmlExtended;

class RepresentativeWriter extends AbstractHeaderWriter
{
    protected function performWrite()
    {
        $documentPerson = $this->document->getRepresentative();

        if ($documentPerson === null) {
            return $this;
        }

        $rappresentanteFiscale = $this->xml->addChild('RappresentanteFiscale');
        $datiAnagrafici        = $rappresentanteFiscale->addChild('DatiAnagrafici');

        $idPaese       = $documentPerson->getCountryCode();
        $codiceFiscale = $documentPerson->getFiscalCode();
        $vatNumber     = $documentPerson->getVatNumber();
        if (empty($codiceFiscale) && empty($vatNumber)) {
            throw new InvalidDocument('Invalid empty fiscal code and vat number for rappresentante fiscale');
        }

        $fiscalData = $this->calculateFiscalData($idPaese, $codiceFiscale, $vatNumber);

        $idFiscaleIva = $datiAnagrafici->addChild('IdFiscaleIVA');
        $idFiscaleIva->addChild('IdPaese', $idPaese);
        $idFiscaleIva->addChild('IdCodice', $fiscalData['idCodice']);

        if (! empty($fiscalData['codiceFiscale'])) {
            $datiAnagrafici->addChild('CodiceFiscale', SimpleXmlExtended::sanitizeText($fiscalData['codiceFiscale']));
        }

        $anagrafica = $datiAnagrafici->addChild('Anagrafica');

        if (! empty($documentPerson->getOrganization())) {
            $anagrafica->addChild('Denominazione', SimpleXmlExtended::sanitizeText($documentPerson->getOrganization()));
        } else {
            if (! empty($documentPerson->getName())) {
                $anagrafica->addChild('Nome', SimpleXmlExtended::sanitizeText($documentPerson->getName()));
            }

            if (! empty($documentPerson->getSurname())) {
                $anagrafica->addChild('Cognome', SimpleXmlExtended::sanitizeText($documentPerson->getSurname()));
            }
        }

        return $this;
    }
}
