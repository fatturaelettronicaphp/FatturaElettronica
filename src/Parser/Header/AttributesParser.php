<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Header;

class AttributesParser extends AbstractHeaderParser
{
    protected function performParsing()
    {
        $version = $this->extractAttributeFromXmlElement($this->xml(), 'versione');
        $system = $this->extractAttributeFromXmlElement($this->xml(), 'SistemaEmittente');

        $this->document->setVersion($version);
        $this->document->setVersion($system);

        return $this->document;
    }
}
