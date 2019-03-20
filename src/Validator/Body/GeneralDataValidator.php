<?php

namespace Weble\FatturaElettronica\Validator\Body;

use Alcohol\ISO4217;
use DomainException;

class GeneralDataValidator extends AbstractBodyValidator
{
    protected function performValidate(): array
    {
        if ($this->body->getDocumentType() === null) {
            $this->errors['//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/TipoDocumento'][] = 'required';
        }

        if ($this->body->getCurrency() === null) {
            $this->errors['//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Divisa'][] = 'required';
        }

        try {
            (new ISO4217())->getByAlpha3($this->body->getCurrency());
        } catch (DomainException $e) {
            $this->errors['//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Divisa'][] = $e->getMessage();
        }

        if ($this->body->getDocumentNumber() === null) {
            $this->errors['//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Numero'][] = 'required';
        }

        return $this->errors;
    }
}
