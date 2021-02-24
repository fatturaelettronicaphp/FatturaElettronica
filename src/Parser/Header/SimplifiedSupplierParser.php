<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Header;

use FatturaElettronicaPhp\FatturaElettronica\Supplier;

class SimplifiedSupplierParser extends AbstractHeaderParser
{
    protected function performParsing()
    {
        if (! $this->document->isSimplified()) {
            return $this->document;
        }

        $supplier = new Supplier();

        $prefix       = '//FatturaElettronicaHeader/CedentePrestatore/';
        $documentName = $this->extractValueFromXml($prefix . 'Nome');
        $supplier->setName($documentName);

        $documentSurname = $this->extractValueFromXml($prefix . 'Cognome');
        $supplier->setSurname($documentSurname);

        $documentOrganization = $this->extractValueFromXml($prefix . 'Denominazione');
        $supplier->setOrganization($documentOrganization);

        $documentFiscalCode = $this->extractValueFromXml($prefix . 'CodiceFiscale');
        $supplier->setFiscalCode($documentFiscalCode);

        $documentVatNumber = $this->extractValueFromXml($prefix . 'IdFiscaleIVA/IdCodice');
        if ($documentVatNumber !== null) {
            $supplier->setVatNumber($documentVatNumber);
        }

        $value = $this->extractValueFromXml($prefix . 'IdFiscaleIVA/IdPaese');
        if ($value !== null) {
            $supplier->setCountryCode($value);
        }

        $addressValue = $this->extractValueFromXml($prefix . 'Sede', false);
        if ($addressValue !== null) {
            $addressValue = array_shift($addressValue);
        }

        if ($addressValue !== null) {
            $address = $this->extractAddressInformationFrom($addressValue);
            $supplier->setAddress($address);
        }

        $addressValue = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/StabileOrganizzazione', false);
        if ($addressValue !== null) {
            $addressValue = array_shift($addressValue);
        }

        if ($addressValue !== null) {
            $address = $this->extractAddressInformationFrom($addressValue);
            $supplier->setForeignFixedAddress($address);
        }

        $value = $this->extractValueFromXml($prefix . 'AlboProfessionale');
        $supplier->setRegister($value);

        $value = $this->extractValueFromXml($prefix . 'ProvinciaAlbo');
        $supplier->setRegisterState($value);

        $value = $this->extractValueFromXml($prefix . 'NumeroIscrizioneAlbo');
        $supplier->setRegisterNumber($value);

        $value = $this->extractValueFromXml($prefix . 'DataIscrizioneAlbo');
        $supplier->setRegisterDate($value);

        $value = $this->extractValueFromXml($prefix . 'RegimeFiscale');
        if ($value !== null) {
            $supplier->setTaxRegime($value);
        }

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/Contatti/Telefono');
        $supplier->setPhone($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/Contatti/Email');
        $supplier->setEmail($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/Contatti/Fax');
        $supplier->setFax($value);

        $value = $this->extractValueFromXml($prefix . 'RiferimentoAmministrazione');
        $supplier->setAdministrativeContact($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/IscrizioneREA/Ufficio');
        $supplier->setReaOffice($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/IscrizioneREA/NumeroREA');
        $supplier->setReaNumber($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/IscrizioneREA/CapitaleSociale');
        $supplier->setCapital($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/IscrizioneREA/SocioUnico');
        $supplier->setAssociateType($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/IscrizioneREA/StatoLiquidazione');
        $supplier->setSettlementType($value);

        $this->document->setSupplier($supplier);

        return $this->document;
    }
}
