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

        $p7mFilePath = $this->convertFromDERtoSMIMEFormat($filePath);

        // php 8 only
        if (
            function_exists('openssl_cms_verify') &&
            openssl_cms_verify($p7mFilePath, OPENSSL_CMS_NOVERIFY + OPENSSL_CMS_NOSIGS, $pemPath, array(__DIR__ . '/ca.pem'), __DIR__ . '/ca.pem', $xmlPath)
        ) {
            return (new XMLDecoder())->decode($xmlPath);
        }

        $exitCode = 0;
        $output = [];
        exec(sprintf('openssl cms -verify -noverify -nosigs -in %s --inform DER  -out %s 2> /dev/null', $pemPath, $xmlPath), $output, $exitCode);

        if ($exitCode !== 0) {
            return null;
        }

        return (new XMLDecoder())->decode($xmlPath);
    }

    protected function convertFromDERtoSMIMEFormat(string $file): string
    {
        $pemPath = tempnam(sys_get_temp_dir(), basename($file));
        $to = <<<TXT
MIME-Version: 1.0
Content-Disposition: attachment; filename="smime.p7m"
Content-Type: application/x-pkcs7-mime; smime-type=signed-data; name="smime.p7m"
Content-Transfer-Encoding: base64
\n
TXT;
        $from = file_get_contents($file);
        $to .= chunk_split(base64_encode($from));
        file_put_contents($pemPath, $to);

        return $pemPath;
    }
}
