<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DeductionInterface;
use FatturaElettronicaPhp\FatturaElettronica\Deduction;

class DeductionParser extends AbstractBodyParser
{
    protected function performParsing()
    {
        $deductions = (array)$this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiRitenuta', false);

        foreach ($deductions as $deduction) {
            $deductionInstance = $this->extractFundInformationsFrom($deduction);
            $this->digitalDocumentInstance->addDeduction($deductionInstance);
        }
    }

    protected function extractFundInformationsFrom($fund): DeductionInterface
    {
        $deductionInstance = new Deduction();
        $value = $this->extractValueFromXmlElement($fund, 'TipoRitenuta');
        $deductionInstance->setType($value);

        $value = $this->extractValueFromXmlElement($fund, 'ImportoRitenuta');
        $deductionInstance->setAmount($value);

        $value = $this->extractValueFromXmlElement($fund, 'AliquotaRitenuta');
        $deductionInstance->setPercentage($value);

        $value = $this->extractValueFromXmlElement($fund, 'CausalePagamento');
        $deductionInstance->setDescription($value);

        return $deductionInstance;
    }
}
