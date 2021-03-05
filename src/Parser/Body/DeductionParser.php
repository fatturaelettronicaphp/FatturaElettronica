<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

class DeductionParser extends AbstractBodyParser
{
    protected function performParsing()
    {
        /**
         * Ritenuta
         */
        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/TipoRitenuta');
        $this->digitalDocymentInstance->setDeductionType($value);

        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/ImportoRitenuta');
        $this->digitalDocymentInstance->setDeductionAmount($value);

        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/AliquotaRitenuta');
        $this->digitalDocymentInstance->setDeductionPercentage($value);

        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/CausalePagamento');
        $this->digitalDocymentInstance->setDeductionDescription($value);
    }
}
