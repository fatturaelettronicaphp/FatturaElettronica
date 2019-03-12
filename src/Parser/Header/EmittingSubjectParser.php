<?php

namespace Weble\FatturaElettronica\Parser\Header;


use Weble\FatturaElettronica\Exceptions\InvalidXmlFile;

class EmittingSubjectParser extends AbstractHeaderParser
{
    protected function performParsing ()
    {
        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/SoggettoEmittente');
        $this->document->setEmittingSubject($code);

        return $this->document;
    }
}
