<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\FundInterface;
use FatturaElettronicaPhp\FatturaElettronica\Fund;

class FundParser extends AbstractBodyParser
{
    protected function performParsing()
    {
        $funds = (array)$this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiCassaPrevidenziale', false);

        foreach ($funds as $fund) {
            $fundInstance = $this->extractFundInformationsFrom($fund);
            $this->digitalDocymentInstance->addFund($fundInstance);
        }
    }

    protected function extractFundInformationsFrom($fund): FundInterface
    {
        $fundInstance = new Fund();
        $value        = $this->extractValueFromXmlElement($fund, 'TipoCassa');
        $fundInstance->setType($value);

        $value = $this->extractValueFromXmlElement($fund, 'AlCassa');
        $fundInstance->setPercentage($value);

        $value = $this->extractValueFromXmlElement($fund, 'ImportoContributoCassa');
        $fundInstance->setAmount($value);

        $value = $this->extractValueFromXmlElement($fund, 'ImponibileCassa');
        $fundInstance->setSubtotal($value);

        $value = $this->extractValueFromXmlElement($fund, 'AliquotaIVA');
        $fundInstance->setTaxPercentage($value);

        $value = $this->extractValueFromXmlElement($fund, 'Ritenuta');
        $fundInstance->setDeduction($value);

        $value = $this->extractValueFromXmlElement($fund, 'Natura');
        $fundInstance->setVatNature($value);

        $value = $this->extractValueFromXmlElement($fund, 'RiferimentoAmministrazione');
        $fundInstance->setRepresentative($value);

        return $fundInstance;
    }
}
