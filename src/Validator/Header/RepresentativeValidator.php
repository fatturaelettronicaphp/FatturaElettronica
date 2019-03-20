<?php

namespace Weble\FatturaElettronica\Validator\Header;

class RepresentativeValidator extends AbstractHeaderValidator
{
    protected function performValidate (): array
    {
        $representative = $this->document->getRepresentative();
        if ($representative === null) {
            return $this->errors;
        }

        if ($representative->getCountryCode() === null) {
            $this->errors['//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/IdFiscaleIVA/IdPaese'][] = 'required';
        }

        $this->validateCountryCode($representative->getCountryCode(), '//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/IdFiscaleIVA/IdPaese');

        if ($representative->getVatNumber() === null) {
            $this->errors['//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/IdFiscaleIVA/IdCodice'][] = 'required';
        }

        $length = strlen($representative->getVatNumber());
        if ($length > 28 || $length < 1) {
            $this->errors['//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/IdFiscaleIVA/IdCodice'][] = 'Length must be [1,28]';
        }

        if ($representative->getOrganization() === null && $representative->getSurname() === null && $representative->getName() === null) {
            $this->errors['//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/Anagrafica'][] = 'required';
        }

        return $this->errors;
    }
}
