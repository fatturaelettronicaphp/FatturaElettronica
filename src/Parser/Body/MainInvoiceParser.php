<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

class MainInvoiceParser extends AbstractBodyParser
{
    protected function performParsing()
    {
        $value = $this->extractValueFromXml('DatiGenerali/NumeroFatturaPrincipale');
        $this->digitalDocumentInstance->setMainInvoiceNumber($value);

        $value = $this->extractValueFromXml('DatiGenerali/DataFatturaPrincipale');
        $this->digitalDocumentInstance->setMainInvoiceDate($value);
    }
}
