<?php

namespace Weble\FatturaElettronica\Contracts;

use SimpleXMLElement;
use Weble\FatturaElettronica\DigitalDocumentInstance;

interface DigitalDocumentParserInterface
{
    public function parse ();

    public function xml (): SimpleXMLElement;
}