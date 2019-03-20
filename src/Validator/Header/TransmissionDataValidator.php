<?php

namespace Weble\FatturaElettronica\Validator\Header;

class TransmissionDataValidator extends AbstractHeaderValidator
{
    protected function performValidate (): array
    {
        if ($this->document->getCountryCode() === null) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdPaese'][] = 'required';
        }

        $this->validateCountryCode($this->document->getCountryCode(), '//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdPaese', false);

        if ($this->document->getSenderVatId() === null) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdCodice'][] = 'required';
        }

        $length = strlen($this->document->getSenderVatId());
        if ($length > 28 || $length < 1) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdCodice'][] = 'Length must be [1,28]';
        }

        if ($this->document->getSendingId() === null) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/ProgressivoInvio'][] = 'required';
        }

        $length = strlen($this->document->getSendingId());
        if ($length > 10 || $length < 1) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/ProgressivoInvio'][] = 'Length must be [1,10]';
        }

        if ($this->document->getTransmissionFormat() === null) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/FormatoTrasmissione'][] = 'required';
        }

        if ($this->document->getCustomerSdiCode() === null) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario'][] = 'required';
        }

        return $this->errors;
    }
}
