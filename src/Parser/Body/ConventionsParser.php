<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

class ConventionsParser extends RelatedDocumentParser
{
    protected function performParsing ()
    {
        $value = (array)$this->extractValueFromXml('DatiGenerali/DatiConvenzione', false);
        foreach ($value as $v) {
            $instance = $this->extractRelatedDocumentInformationsFrom($v);
            $this->digitalDocymentInstance->addConvention($instance);
        }
    }
}
