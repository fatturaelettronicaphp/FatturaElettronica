<?php

namespace Weble\FatturaElettronica\Validator\Header;

use Weble\FatturaElettronica\Enums\TransmissionFormat;

class IntermediaryValidator extends AbstractHeaderValidator
{
    protected function performValidate (): array
    {
        $intermediary = $this->document->getIntermediary();
        if ($intermediary === null) {
            return $this->errors;
        }

        if ($intermediary->getCountryCode() === null) {
            $this->errors['//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/IdFiscaleIVA/IdPaese'][] = 'required';
        }

        $this->validateCountryCode($intermediary->getCountryCode(), '//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/IdFiscaleIVA/IdPaese');

        if ($intermediary->getVatNumber() === null) {
            $this->errors['//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/IdFiscaleIVA/IdCodice'][] = 'required';
        }

        $length = strlen($intermediary->getVatNumber());
        if ($length > 28 || $length < 1) {
            $this->errors['//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/IdFiscaleIVA/IdCodice'][] = 'Length must be [1,28]';
        }

        if ($intermediary->getOrganization() === null && $intermediary->getSurname() === null && $intermediary->getName() === null) {
            $this->errors['//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/Anagrafica'][] = 'required';
        }

        return $this->errors;
    }
}
