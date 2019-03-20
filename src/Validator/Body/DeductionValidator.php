<?php

namespace Weble\FatturaElettronica\Validator\Body;

use Alcohol\ISO4217;
use DomainException;

class DeductionValidator extends AbstractBodyValidator
{
    protected function performValidate(): array
    {
        if (!$this->body->hasDeduction()) {
            return $this->errors;
        }
        if ($this->body->getDeductionType() === null) {
            $this->errors['//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/TipoRitenuta'][] = 'required';
        }

        if ($this->body->getDeductionPercentage() === null) {
            $this->errors['//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/AliquotaRitenuta'][] = 'required';
        }

        if ($this->body->getDeductionAmount() === null) {
            $this->errors['//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/ImportoRitenuta'][] = 'required';
        }

        if ($this->body->getDeductionDescription() === null) {
            $this->errors['//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/CausalePagamento'][] = 'required';
        }

        return $this->errors;
    }
}
