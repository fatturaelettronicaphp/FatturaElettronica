<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer\Header;

use FatturaElettronicaPhp\FatturaElettronica\Enums\TaxRegime;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidDocument;
use FatturaElettronicaPhp\FatturaElettronica\Supplier;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\SimpleXmlExtended;

class SimplifiedSupplierWriter extends AbstractHeaderWriter
{
    protected function performWrite()
    {
        $cedentePrestatore = $this->xml->addChild('CedentePrestatore');

        /** @var Supplier $supplier */
        $supplier = $this->document->getSupplier();
        $idPaese  = $supplier->getCountryCode();
        $codiceFiscale = $supplier->getFiscalCode();
        $vatNumber     = $supplier->getVatNumber();

        $fiscalData = $this->calculateFiscalData($idPaese, $codiceFiscale, $vatNumber);

        $idFiscaleIva = $cedentePrestatore->addChild('IdFiscaleIVA');
        $idFiscaleIva->addChild('IdPaese', $idPaese);
        $idFiscaleIva->addChild('IdCodice', SimpleXmlExtended::sanitizeText($fiscalData['idCodice']));

        if (! empty($fiscalData['codiceFiscale'])) {
            $cedentePrestatore->addChild('CodiceFiscale', SimpleXmlExtended::sanitizeText($fiscalData['codiceFiscale']));
        }

        if (! empty($supplier->getOrganization())) {
            $cedentePrestatore->addChild('Denominazione', SimpleXmlExtended::sanitizeText($supplier->getOrganization()));
        } else {
            if (! empty($supplier->getName())) {
                $cedentePrestatore->addChild('Nome', SimpleXmlExtended::sanitizeText($supplier->getName()));
            }

            if (! empty($supplier->getSurname())) {
                $cedentePrestatore->addChild('Cognome', SimpleXmlExtended::sanitizeText($supplier->getSurname()));
            }
        }

        if (! empty($supplier->getRegister())) {
            $cedentePrestatore->addChild('AlboProfessionale', SimpleXmlExtended::sanitizeText($supplier->getRegister()));
        }

        if (! empty($supplier->getRegisterState())) {
            $cedentePrestatore->addChild('ProvinciaAlbo', strtoupper(SimpleXmlExtended::sanitizeText($supplier->getRegisterState())));
        }

        if (! empty($supplier->getRegisterNumber())) {
            $cedentePrestatore->addChild('NumeroIscrizioneAlbo', SimpleXmlExtended::sanitizeText($supplier->getRegisterNumber()));
        }

        if (! empty($supplier->getRegisterDate())) {
            $cedentePrestatore->addChild('DataIscrizioneAlbo', $supplier->getRegisterDate()->format('Y-m-d\TH:i:s.000P'));
        }

        $sede = $cedentePrestatore->addChild('Sede');

        $address = $supplier->getAddress();
        $sede->addChild('Indirizzo', SimpleXmlExtended::sanitizeText($address->getStreet()));

        if (! empty($address->getStreetNumber())) {
            $sede->addChild('NumeroCivico', SimpleXmlExtended::sanitizeText($address->getStreetNumber()));
        }

        $sanitizedCap = preg_replace("/[^0-9]/", '', $address->getZip());
        $sanitizedCap = substr($sanitizedCap, 0, 5);
        $sanitizedCap = str_pad($sanitizedCap, 5, '0', STR_PAD_LEFT);

        $sede->addChild('CAP', $sanitizedCap);
        $sede->addChild('Comune', SimpleXmlExtended::sanitizeText($address->getCity()));

        if (! empty($address->getState())) {
            $sede->addChild('Provincia', strtoupper(SimpleXmlExtended::sanitizeText($address->getState())));
        }
        $sede->addChild('Nazione', $address->getCountryCode());

        if ($supplier->getReaNumber() && $supplier->getReaOffice()) {
            $iscrizioneRea = $cedentePrestatore->addChild('IscrizioneREA');

            $iscrizioneRea->addChild('Ufficio', SimpleXmlExtended::sanitizeText($supplier->getReaOffice()));
            $iscrizioneRea->addChild('NumeroREA', SimpleXmlExtended::sanitizeText($supplier->getReaNumber()));

            if (! empty($supplier->getCapital())) {
                $iscrizioneRea->addChild('CapitaleSociale', $supplier->getCapital());
            }

            if (! empty($supplier->getAssociateType())) {
                $iscrizioneRea->addChild('SocioUnico', SimpleXmlExtended::sanitizeText((string)$supplier->getAssociateType()));
            }

            $iscrizioneRea->addChild('StatoLiquidazione', SimpleXmlExtended::sanitizeText((string)$supplier->getSettlementType()));
        }

        if ($supplier->getTaxRegime()) {
            $cedentePrestatore->addChild('RegimeFiscale', (string)$supplier->getTaxRegime());
        } else {
            $cedentePrestatore->addChild('RegimeFiscale', (string)TaxRegime::RF01());
        }

        if ($supplier->hasContacts()) {
            $contatti = $cedentePrestatore->addChild('Contatti');

            if ($supplier->getPhone() !== null) {
                $contatti->addChild('Telefono', SimpleXmlExtended::sanitizeText($supplier->getPhone()));
            }
            if ($supplier->getFax() !== null) {
                $contatti->addChild('Fax', SimpleXmlExtended::sanitizeText($supplier->getFax()));
            }
            if ($supplier->getEmail() !== null) {
                $contatti->addChild('Email', SimpleXmlExtended::sanitizeText($supplier->getEmail()));
            }
        }

        return $this;
    }
}
