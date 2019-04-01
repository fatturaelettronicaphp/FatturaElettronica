<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

class VirtualDutyParser extends AbstractBodyParser
{
    protected function performParsing ()
    {
        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiBollo/BolloVirtuale');
        $this->digitalDocymentInstance->setVirtualDuty($value);

        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiBollo/ImportoBollo');
        $this->digitalDocymentInstance->setVirtualDutyAmount($value);
    }
}
