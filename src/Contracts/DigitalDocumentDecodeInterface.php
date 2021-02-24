<?php


namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;


use SimpleXMLElement;

interface DigitalDocumentDecodeInterface
{
    public function decode(string $filePath): ?SimpleXMLElement;
}
