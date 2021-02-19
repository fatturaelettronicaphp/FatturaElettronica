<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\CustomerParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\EmittingSubjectParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\IntermediaryParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\RepresentativeParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\SupplierParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\TransmissionDataParser;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Pipeline;
use SimpleXMLElement;

class DigitalDocumentHeaderParser implements DigitalDocumentParserInterface
{
    use XmlUtilities;

    /** @var SimpleXMLElement */
    protected $xml;

    public function __construct(SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    public function parse(DigitalDocumentInterface $digitalDocument = null): DigitalDocumentInterface
    {
        if ($digitalDocument === null) {
            $digitalDocument = new DigitalDocument();
        }

        $parserPipeline = new Pipeline();

        return $parserPipeline
            ->send($digitalDocument)
            ->with($this->xml())
            ->usingMethod('parse')
            ->through([
                TransmissionDataParser::class,
                CustomerParser::class,
                SupplierParser::class,
                RepresentativeParser::class,
                IntermediaryParser::class,
                EmittingSubjectParser::class,
            ])
            ->thenReturn();
    }

    public function xml(): SimpleXMLElement
    {
        return $this->xml;
    }
}
