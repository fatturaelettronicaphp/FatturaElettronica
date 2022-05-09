<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

class VirtualDutyParser extends AbstractBodyParser
{
    protected function performParsing()
    {
        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiBollo/BolloVirtuale');
        $this->digitalDocumentInstance->setVirtualDuty($value);

        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiBollo/ImportoBollo');
        $this->digitalDocumentInstance->setVirtualDutyAmount($value);
    }
}
