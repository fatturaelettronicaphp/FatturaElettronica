<?php

namespace Weble\FatturaElettronica\Validator\Header;

class SupplierValidator extends AbstractHeaderValidator
{
    protected function performValidate (): array
    {
        $supplier = $this->document->getSupplier();

        if ($supplier === null) {
            $this->errors['//FatturaElettronicaHeader/CedentePrestatore'][] = 'required';
            return $this->errors;
        }

        $this->validateCountryCode($supplier->getCountryCode(), '//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdPaese');

        if ($supplier->getVatNumber() === null) {
            $this->errors['//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdCodice'][] = 'required';
        }

        $length = strlen($supplier->getVatNumber());
        if ($length > 28 || $length < 1) {
            $this->errors['//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdCodice'][] = 'Length must be [1,28]';
        }

        if ($supplier->getOrganization() === null && $supplier->getSurname() === null && $supplier->getName() === null) {
            $this->errors['//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica'][] = 'required';
        }

        if ($supplier->getTaxRegime() === null) {
            $this->errors['//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/RegimeFiscale'][] = 'required';
        }

        $this->validateAddress($supplier->getAddress(), '//FatturaElettronicaHeader/CedentePrestatore/Sede');

        if ($supplier->getForeignFixedAddress()) {
            $this->validateAddress($supplier->getForeignFixedAddress(), '//FatturaElettronicaHeader/CedentePrestatore/StabileOrganizzazione');
        }

        return $this->errors;
    }
}
