<?php


namespace FatturaElettronicaPhp\FatturaElettronica\Decoder;


use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentDecodeInterface;
use SimpleXMLElement;

class SMIMEBase64Decoder extends SMIMEDecoder implements DigitalDocumentDecodeInterface
{
    protected function convertFromDERtoSMIMEFormat(string $file): string
    {
        $pemPath = tempnam(sys_get_temp_dir(), basename($file));
        $to      = <<<TXT
MIME-Version: 1.0
Content-Disposition: attachment; filename="smime.p7m"
Content-Type: application/x-pkcs7-mime; smime-type=signed-data; name="smime.p7m"
Content-Transfer-Encoding: base64
\n
TXT;
        $from = file_get_contents($file);
        $to .= chunk_split($from);
        file_put_contents($pemPath, $to);

        return $pemPath;
    }
}
