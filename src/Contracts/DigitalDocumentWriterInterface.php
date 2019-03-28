<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use SimpleXMLElement;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocumentInstance;

interface DigitalDocumentWriterInterface
{
    public function xml (): SimpleXMLElement;

    public function generate(): DigitalDocumentWriterInterface;

    public function write($filePath): bool ;
}