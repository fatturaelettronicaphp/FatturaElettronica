<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;
use FatturaElettronicaPhp\FatturaElettronica\SimplifiedLine;

class SimplifiedProductsParser extends AbstractBodyParser
{
    protected function performParsing()
    {
        $line = new SimplifiedLine();

        $value = $this->extractValueFromXmlElement($this->xml, 'DatiBeniServizi/Descrizione');
        $line->setDescription($value);

        $value = $this->extractValueFromXmlElement($this->xml, 'DatiBeniServizi/Importo');
        $line->setTotal($value);

        $value = $this->extractValueFromXmlElement($this->xml, 'DatiBeniServizi/DatiIVA/Imposta');
        $line->setTaxAmount($value);

        $value = $this->extractValueFromXmlElement($this->xml, 'DatiBeniServizi/DatiIVA/Aliquota');
        $line->setTaxPercentage($value);

        $value = $this->extractValueFromXmlElement($this->xml, 'DatiBeniServizi/Natura');
        if ($value) {
            $line->setVatNature(new VatNature($value));
        }

        $value = $this->extractValueFromXmlElement($this->xml, 'DatiBeniServizi/RiferimentoNormativo');
        if ($value) {
            $line->setReference($value);
        }

        $this->digitalDocymentInstance->setSimplifiedLine($line);
    }
}
