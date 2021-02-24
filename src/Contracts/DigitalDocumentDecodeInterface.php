<?php


namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;


interface DigitalDocumentDecodeInterface
{
    public function decode(string $filePath): ?string;
}
