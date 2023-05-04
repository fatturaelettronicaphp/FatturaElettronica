<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use FatturaElettronicaPhp\FatturaElettronica\ShippingLabel;

class ShippingLabelsParser extends AbstractBodyParser
{
    protected function performParsing()
    {
        $value = (array)$this->extractValueFromXml('DatiGenerali/DatiDDT', false);
        foreach ($value as $v) {
            $instance = $this->extractShippingLabelInformationsFrom($v);
            $this->digitalDocumentInstance->addShippingLabel($instance);
        }
    }

    protected function extractShippingLabelInformationsFrom($order): ShippingLabel
    {
        $instance = new ShippingLabel();

        $value = $this->extractValueFromXmlElement($order, 'RiferimentoNumeroLinea', false);
        if($value !== null ) {
            foreach ($value as $v) {
                $instance->addLineNumberReference((string)$v);
            }
        }

        $value = $this->extractValueFromXmlElement($order, 'NumeroDDT');
        $instance->setDocumentNumber($value);

        $value = $this->extractValueFromXmlElement($order, 'DataDDT');
        $instance->setDocumentDate($value);

        return $instance;
    }
}
