<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Decoder;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentDecodeInterface;
use SimpleXMLElement;

class CMSDecoder implements DigitalDocumentDecodeInterface
{
    public function decode(string $filePath): ?SimpleXMLElement
    {
        $xmlPath = tempnam(sys_get_temp_dir(), basename($filePath));
        $pemPath = tempnam(sys_get_temp_dir(), basename($filePath));

        // php 8 only
        if (
            function_exists('openssl_cms_verify') &&
            openssl_cms_verify($filePath, OPENSSL_CMS_NOVERIFY + OPENSSL_CMS_NOSIGS, $pemPath, [__DIR__ . '/ca.pem'], __DIR__ . '/ca.pem', $xmlPath)
        ) {
            return (new XMLDecoder())->decode($xmlPath);
        }

        $exitCode = 0;
        $output   = [];
        exec(sprintf('openssl cms -verify -noverify -nosigs -in %s --inform DER  -out %s 2> /dev/null', $pemPath, $xmlPath), $output, $exitCode);

        if ($exitCode !== 0) {
            return null;
        }

        return (new XMLDecoder())->decode($xmlPath);
    }
}
