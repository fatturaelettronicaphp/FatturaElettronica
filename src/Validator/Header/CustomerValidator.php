<?php

namespace Weble\FatturaElettronica\Validator\Header;

use Weble\FatturaElettronica\Enums\TransmissionFormat;

class CustomerValidator extends AbstractHeaderValidator
{
    protected function performValidate(): array
    {
        $customer = $this->document->getCustomer();
        if ($customer === null) {
            $this->errors['//FatturaElettronicaHeader/CessionarioCommittente'][] = 'required';
            return $this->errors;
        }

        if ($customer->getCountryCode() !== null && $customer->getVatNumber() !== null) {
            $this->validateCountryCode($customer->getCountryCode(),
                    '//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/IdFiscaleIVA/IdPaese');
        }

        if ($customer->getVatNumber() === null && $customer->getFiscalCode() === null) {
            $this->errors['//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/IdFiscaleIVA/IdCodice'][] = 'required';
            $this->errors['//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/CodiceFiscale'][] = 'required';
        }

        $length = strlen($customer->getVatNumber());

        if ($customer->getVatNumber() !== null && $length > 28) {
            $this->errors['//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/IdFiscaleIVA/IdCodice'][] = 'Length must be [1,28]';
        }

        if ($customer->getOrganization() === null && $customer->getSurname() === null && $customer->getName() === null) {
            $this->errors['//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica'][] = 'required';
        }

        $this->validateAddress($customer->getAddress(), '//FatturaElettronicaHeader/CessionarioCommittente/Sede');

        if ($customer->getForeignFixedAddress()) {
            $this->validateAddress($customer->getForeignFixedAddress(),
                '//FatturaElettronicaHeader/CessionarioCommittente/StabileOrganizzazione');
        }

        if ($this->document->getTransmissionFormat()->equals(TransmissionFormat::FPR12())) {
            $representative = $customer->getRepresentative();

            if ($representative === null) {
                return $this->errors;
            }

            if ($representative->getCountryCode() === null) {
                $this->errors['//FatturaElettronicaHeader/CessionarioCommittente/RappresentanteFiscale/IdFiscaleIVA/IdPaese'][] = 'required';
            } else {
                $this->validateCountryCode($representative->getCountryCode(),
                    '//FatturaElettronicaHeader/CessionarioCommittente/RappresentanteFiscale/IdFiscaleIVA/IdPaese');
            }

            if ($representative->getVatNumber() === null) {
                $this->errors['//FatturaElettronicaHeader/CessionarioCommittente/RappresentanteFiscale/IdFiscaleIVA/IdCodice'][] = 'required';
            }

            if ($representative->getOrganization() === null && $representative->getSurname() === null && $representative->getName() === null) {
                if ($representative->getOrganization() === null) {
                    $this->errors['//FatturaElettronicaHeader/CessionarioCommittente/RappresentanteFiscale/Denominazione'][] = 'required';
                    $this->errors['//FatturaElettronicaHeader/CessionarioCommittente/RappresentanteFiscale/Nome'][] = 'required';
                    $this->errors['//FatturaElettronicaHeader/CessionarioCommittente/RappresentanteFiscale/Cognome'][] = 'required';
                }
            }

        } else {
            $this->errors['//FatturaElettronicaHeader/CessionarioCommittente/RappresentanteFiscale'][] = 'Cannot use with FPA12 format.';
        }

        return $this->errors;
    }
}
