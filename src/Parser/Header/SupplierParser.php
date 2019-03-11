<?php

namespace Weble\FatturaElettronica\Parser\Header;


use Weble\FatturaElettronica\Customer;
use Weble\FatturaElettronica\Exceptions\InvalidXmlFile;
use Weble\FatturaElettronica\Representative;
use Weble\FatturaElettronica\Supplier;

class SupplierParser extends AbstractHeaderParser
{
    protected function performParsing ()
    {

        $supplier = new Supplier();

        $documentName = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Nome');
        $supplier->setName($documentName);

        $documentSurname = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Cognome');
        $supplier->setSurname($documentSurname);

        $documentOrganization = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Denominazione');
        $supplier->setOrganization($documentOrganization);

        $documentFiscalCode = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/CodiceFiscale');
        $supplier->setFiscalCode($documentFiscalCode);

        $documentVatNumber = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdCodice');
        if ($documentVatNumber === null) {
            throw new InvalidXmlFile('Vat Number is required');
        }
        $supplier->setVatNumber($documentVatNumber);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdPaese');
        if ($value === null) {
            throw new InvalidXmlFile('IdPaese is required');
        }
        $supplier->setCountryCode($value);

        $addressValue = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/Sede', false);
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

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/AlboProfessionale');
        $supplier->setRegister($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/ProvinciaAlbo');
        $supplier->setRegisterState($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/NumeroIscrizioneAlbo');
        $supplier->setRegisterNumber($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/DataIscrizioneAlbo');
        $supplier->setRegisterDate($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/RegimeFiscale');
        $supplier->setTaxRegime($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Contatti/Telefono');
        $supplier->setPhone($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Contatti/Email');
        $supplier->setEmail($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Contatti/Fax');
        $supplier->setFax($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/RiferimentoAmministrazione');
        $supplier->setAdministrativeContact($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IscrizioneRea/Ufficio');
        $supplier->setReaOffice($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IscrizioneRea/NumeroREA');
        $supplier->setReaNumber($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IscrizioneRea/CapitaleSociale');
        $supplier->setCapital($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IscrizioneRea/SocioUnico');
        $supplier->setAssociateType($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IscrizioneRea/StatoLiquidazione');
        $supplier->setSettlementType($value);

        $this->document->setSupplier($supplier);

        return $this->document;
    }
}
