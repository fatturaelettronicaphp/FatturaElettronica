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
            $this->digitalDocymentInstance->addShippingLabel($instance);
        }
    }

    protected function extractShippingLabelInformationsFrom($order): ShippingLabel
    {
        $instance = new ShippingLabel();

        $value = $this->extractValueFromXmlElement($order, 'RiferimentoNumeroLinea');
        $instance->setLineNumberReference($value);

        $value = $this->extractValueFromXmlElement($order, 'IdDocumento');
        $instance->setDocumentNumber($value);

        $value = $this->extractValueFromXmlElement($order, 'Data');
        $instance->setDocumentDate($value);

        return $instance;
    }
}
