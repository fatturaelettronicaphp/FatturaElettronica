<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

class RecepitsParser extends RelatedDocumentParser
{
    protected function performParsing()
    {
        $value = (array)$this->extractValueFromXml('DatiGenerali/DatiRicezione', false);
        foreach ($value as $v) {
            $instance = $this->extractRelatedDocumentInformationsFrom($v);
            $this->digitalDocumentInstance->addReceipt($instance);
        }
    }
}
