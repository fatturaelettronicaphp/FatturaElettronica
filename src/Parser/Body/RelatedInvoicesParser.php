<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

class RelatedInvoicesParser extends RelatedDocumentParser
{
    protected function performParsing ()
    {
        $value = (array)$this->extractValueFromXml('DatiGenerali/DatiFattureCollegate', false);
        foreach ($value as $v) {
            $instance = $this->extractRelatedDocumentInformationsFrom($v);
            $this->digitalDocymentInstance->addRelatedInvoice($instance);
        }
    }
}
