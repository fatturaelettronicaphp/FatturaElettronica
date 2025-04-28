<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Validator\Check;

use BackedEnum;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentValidatorInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\TransmissionFormat;
use FatturaElettronicaPhp\FatturaElettronica\Validator\BasicDigitalDocumentValidator;

class SdiCodeLength extends BasicDigitalDocumentValidator
{
    public function validate(): DigitalDocumentValidatorInterface
    {
        $sdiCode = $this->document->getCustomerSdiCode();
        $format = $this->document->getTransmissionFormat();

        if ($sdiCode instanceof BackedEnum) {
            $sdiCode = $sdiCode->value;
        }

        if ($format === null) {
            return $this;
        }

        if ($format === TransmissionFormat::FPA12 && strlen($sdiCode) !== 6) {
            $this->errors['FatturaElettronicaHeader.DatiTrasmissione.CodiceDestinatario'] = "Errore 00427: 1.1.4 <CodiceDestinatario> deve essere di 6 caratteri a fronte di 1.1.3 <FormatoTrasmissione> con valore FPA12";

            return $this;
        }

        if ($format !== TransmissionFormat::FPA12 && strlen($sdiCode) !== 7) {
            $this->errors['FatturaElettronicaHeader.DatiTrasmissione.CodiceDestinatario'] = "Errore 00427: 1.1.4 <CodiceDestinatario> deve essere di 7 caratteri a fronte di 1.1.3 <FormatoTrasmissione> con valore FPR12 o FSM10";
        }

        return $this;
    }
}
