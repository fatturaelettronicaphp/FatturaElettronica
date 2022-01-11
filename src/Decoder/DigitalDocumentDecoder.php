<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Decoder;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentDecodeInterface;
use SimpleXMLElement;

class DigitalDocumentDecoder implements DigitalDocumentDecodeInterface
{
    protected const STANDARD_DECODERS = [
        XMLDecoder::class,
        SMIMEDecoder::class,
        SMIMEBase64Decoder::class,
        CMSDecoder::class,
    ];

    /**
     * @var array
     */
    protected $decoders = [];

    public function __construct()
    {
        $this->decoders = array_map(function (string $decoderClass) {
            return new $decoderClass();
        }, self::STANDARD_DECODERS);
    }

    public function decode(string $filePath): ?SimpleXMLElement
    {
        foreach ($this->decoders as $decoder) {
            $file = (new $decoder())->decode($filePath);
            if ($file) {
                return $file;
            }
        }

        return null;
    }

    public function addDecoder(DigitalDocumentDecodeInterface $decoder): self
    {
        // Add to the top of the list
        array_unshift($this->decoders, $decoder);

        return $this;
    }
}
