<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer\Header;

use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidDocument;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\SimpleXmlExtended;

class IntermediaryWriter extends AbstractHeaderWriter
{
    protected function performWrite ()
    {
        $intermediary = $this->document->getIntermediary();

        if (!$intermediary) {
            return $this;
        }

        $intermediario = $this->xml->addChild('TerzoIntermediarioOSoggettoEmittente');
        $datiAnagrafici = $intermediario->addChild('DatiAnagrafici');

        $idPaese = $intermediary->getCountryCode();
        if (empty($idPaese)) {
            throw new InvalidDocument('Invalid empty id paese for terzo intermediario');
        }

        $codiceFiscale = $intermediary->getFiscalCode();
        if (empty($codiceFiscale)) {
            throw new InvalidDocument('Invalid empty fiscal code for terzo intermediario');
        }

        $fiscalData = $this->calculateFiscalData($idPaese, $codiceFiscale, $intermediary->getVatNumber());

        if (!empty($fiscalData['idCodice'])) {
            $idFiscaleIva = $datiAnagrafici->addChild('IdFiscaleIVA');
            $idFiscaleIva->addChild('IdPaese', $idPaese);
            $idFiscaleIva->addChild('IdCodice', SimpleXmlExtended::sanitizeText($fiscalData['idCodice']));
        }

        if (!empty($fiscalData['codiceFiscale'])) {
            $datiAnagrafici->addChild('CodiceFiscale', SimpleXmlExtended::sanitizeText($fiscalData['codiceFiscale']));
        }

        $anagrafica = $datiAnagrafici->addChild('Anagrafica');

        if (!empty($intermediary->getOrganization())) {
            $anagrafica->addChild('Denominazione', SimpleXmlExtended::sanitizeText($intermediary->getOrganization()));

        } else {
            if (!empty($intermediary->getName())) {
                $anagrafica->addChild('Nome', SimpleXmlExtended::sanitizeText($intermediary->getName()));
            }

            if (!empty($intermediary->getSurname())) {
                $anagrafica->addChild('Cognome', SimpleXmlExtended::sanitizeText($intermediary->getSurname()));
            }
        }
    }

}
