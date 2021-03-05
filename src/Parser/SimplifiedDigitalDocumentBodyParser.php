<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\AttachmentParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\GeneralDataParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\SimplifiedProductsParser;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Pipeline;

class SimplifiedDigitalDocumentBodyParser extends AbstractDigitalDocumentBodyParser implements DigitalDocumentParserInterface
{
    public function parse(): DigitalDocumentInstanceInterface
    {
        return (new Pipeline())
            ->send($this->digitalDocumentInstance)
            ->with($this->xml())
            ->usingMethod('parse')
            ->through([
                GeneralDataParser::class,
                SimplifiedProductsParser::class,
                AttachmentParser::class,
            ])
            ->thenReturn();
    }
}
