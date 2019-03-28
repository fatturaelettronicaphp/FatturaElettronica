<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use SimpleXMLElement;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocumentInstance;

interface DigitalDocumentParserInterface
{
    public function parse ();

    public function xml (): SimpleXMLElement;
}