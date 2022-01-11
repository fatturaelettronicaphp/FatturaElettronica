<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\TotalInterface;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidXmlFile;
use FatturaElettronicaPhp\FatturaElettronica\Total;

class SummaryParser extends AbstractBodyParser
{
    protected function performParsing()
    {
        $totals = (array) $this->extractValueFromXml('DatiBeniServizi/DatiRiepilogo', false);
        $amount = 0;
        $amountTax = 0;

        foreach ($totals as $totalValue) {
            $total = $this->extractTotalFrom($totalValue);
            $this->digitalDocymentInstance->addTotal($total);
        }
    }

    /**
     * @param $total
     *
     * @return Total
     */
    protected function extractTotalFrom($total): TotalInterface
    {
        $instance = new Total();

        $taxPercentage = $this->extractValueFromXmlElement($total, 'AliquotaIVA');
        $nature = $this->extractValueFromXmlElement($total, 'Natura');
        $type = $this->extractValueFromXmlElement($total, 'EsigibilitaIVA');
        $expenses = $this->extractValueFromXmlElement($total, 'SpeseAccessorie');
        $rounding = $this->extractValueFromXmlElement($total, 'Arrotondamento');
        $totalValue = $this->extractValueFromXmlElement($total, 'ImponibileImporto');
        $tax = $this->extractValueFromXmlElement($total, 'Imposta');
        $reference = $this->extractValueFromXmlElement($total, 'RiferimentoNormativo');

        if ($totalValue === null) {
            throw new InvalidXmlFile('<ImponibileImporto> not found');
        }

        if ($tax === null) {
            throw new InvalidXmlFile('<Imposta> not found');
        }

        $instance
            ->setTaxAmount($tax)
            ->setTaxPercentage($taxPercentage)
            ->setVatNature($nature)
            ->setOtherExpenses($expenses)
            ->setRounding($rounding)
            ->setTotal($totalValue)
            ->setTaxType($type)
            ->setReference($reference);

        return $instance;
    }
}
