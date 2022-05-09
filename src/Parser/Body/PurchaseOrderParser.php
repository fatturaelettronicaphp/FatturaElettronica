<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

class PurchaseOrderParser extends RelatedDocumentParser
{
    protected function performParsing()
    {
        $value = (array)$this->extractValueFromXml('DatiGenerali/DatiOrdineAcquisto', false);
        foreach ($value as $v) {
            $instance = $this->extractRelatedDocumentInformationsFrom($v);
            $this->digitalDocumentInstance->addPurchaseOrder($instance);
        }
    }
}
