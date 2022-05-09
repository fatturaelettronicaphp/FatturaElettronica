<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

class SalParser extends AbstractBodyParser
{
    protected function performParsing()
    {
        $value = (array)$this->extractValueFromXml('DatiGenerali/DatiSal', false);
        foreach ($value as $v) {
            $this->digitalDocumentInstance->addSal($v);
        }
    }
}
