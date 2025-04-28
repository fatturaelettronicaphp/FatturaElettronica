<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Header;

use FatturaElettronicaPhp\FatturaElettronica\Enums\EmittingSubject;
use FatturaElettronicaPhp\FatturaElettronica\Enums\TransmissionFormat;

class AttributesParser extends AbstractHeaderParser
{
    protected function performParsing()
    {
        $version = $this->extractAttributeFromXmlElement($this->xml(), 'versione');
        $system = $this->extractAttributeFromXmlElement($this->xml(), 'SistemaEmittente');

        $this->document->setVersion(TransmissionFormat::from($version));
        $this->document->setEmittingSubject(EmittingSubject::tryFrom($system));

        return $this->document;
    }
}
