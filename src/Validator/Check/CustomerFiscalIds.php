<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Validator\Check;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentValidatorInterface;
use FatturaElettronicaPhp\FatturaElettronica\Validator\BasicDigitalDocumentValidator;

class CustomerFiscalIds extends BasicDigitalDocumentValidator
{
    use ChecksForValidFiscalCodes;

    public function validate(): DigitalDocumentValidatorInterface
    {
        if (! $this->hasVatIdOrFiscaleCode($this->document->getCustomer())) {
            $this->errors['FatturaElettronicaHeader.DatiAnagrafici.IdFiscaleIVA'] = "Errore 00417:1.4.1.1 <IdFiscaleIVA> e 1.4.1.2 <CodiceFiscale> non valorizzati (almeno uno dei due deve essere valorizzato)";
        }

        return $this;
    }
}
