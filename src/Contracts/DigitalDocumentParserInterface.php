<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use SimpleXMLElement;

interface DigitalDocumentParserInterface
{
    public function parse();

    public function xml(): SimpleXMLElement;
}
