<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;
use FatturaElettronicaPhp\FatturaElettronica\SimplifiedLine;

class SimplifiedProductsParser extends AbstractBodyParser
{
    protected function performParsing()
    {
        $rows = $this->extractValueFromXmlElement($this->xml, 'DatiBeniServizi', false);

        if (! $rows || ! is_array($rows)) {
            return;
        }
        foreach ($rows as $row) {
            $line = new SimplifiedLine();

            $value = $this->extractValueFromXmlElement($row, 'Descrizione');
            $line->setDescription($value);

            $value = $this->extractValueFromXmlElement($row, 'Importo');
            $line->setTotal($value);

            $value = $this->extractValueFromXmlElement($row, 'DatiIVA/Imposta');
            $line->setTaxAmount($value);

            $value = $this->extractValueFromXmlElement($row, 'DatiIVA/Aliquota');
            $line->setTaxPercentage($value);

            $value = $this->extractValueFromXmlElement($row, 'Natura');
            if ($value) {
                $line->setVatNature(new VatNature($value));
            }

            $value = $this->extractValueFromXmlElement($row, 'RiferimentoNormativo');
            if ($value) {
                $line->setReference($value);
            }

            $this->digitalDocumentInstance->addSimplifiedLine($line);
        }
    }
}
