<?php


namespace FatturaElettronicaPhp\FatturaElettronica\Decoder;


use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentDecodeInterface;
use SimpleXMLElement;

class XMLDecoder implements DigitalDocumentDecodeInterface
{
    public function decode(string $filePath): ?SimpleXMLElement
    {
        libxml_use_internal_errors(true);
        $simpleXml = simplexml_load_string(file_get_contents($filePath), 'SimpleXMLElement', LIBXML_NOERROR + LIBXML_NOWARNING);
        if (!$simpleXml) {
            return null;
        }

        return $simpleXml;

    }
}
