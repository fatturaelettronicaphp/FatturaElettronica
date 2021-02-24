<?php


namespace FatturaElettronicaPhp\FatturaElettronica\Decoder;


use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentDecodeInterface;

class XMLDecoder implements DigitalDocumentDecodeInterface
{
    public function decode(string $filePath): ?string
    {
        libxml_use_internal_errors(true);

        try {
            if (!simplexml_load_file($filePath)) {
                return null;
            }

            return $filePath;
        } catch (\Exception $e) {

        }

        return null;
    }
}
