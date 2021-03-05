<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidXmlFile;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\CustomerParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\EmittingSubjectParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\IntermediaryParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\RepresentativeParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\SimplifiedCustomerParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\SimplifiedSupplierParser;
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
        $digitalDocument = $this->detectTransmissionFormat($digitalDocument);

        $parserPipeline = new Pipeline();
        $parserPipeline
            ->with($this->xml())
            ->send($digitalDocument)
            ->usingMethod('parse');

        if ($digitalDocument->isSimplified()) {
            return $parserPipeline
                ->through([
                    TransmissionDataParser::class,
                    SimplifiedCustomerParser::class,
                    SimplifiedSupplierParser::class,
                    EmittingSubjectParser::class,
                ])
                ->thenReturn();
        }

        return $parserPipeline
            ->through([
                TransmissionDataParser::class,
                CustomerParser::class,
                SupplierParser::class,
                RepresentativeParser::class,
                IntermediaryParser::class,
                EmittingSubjectParser::class,
            ])->thenReturn();
    }

    public function xml(): SimpleXMLElement
    {
        return $this->xml;
    }

    protected function detectTransmissionFormat(?DigitalDocumentInterface $digitalDocument = null): DigitalDocumentInterface
    {
        if ($digitalDocument === null) {
            $digitalDocument = new DigitalDocument();
        }

        $transmissionFormat = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/FormatoTrasmissione');
        if ($transmissionFormat === null) {
            throw new InvalidXmlFile('Transmission Format not found');
        }

        $digitalDocument->setTransmissionFormat($transmissionFormat);

        return $digitalDocument;
    }
}
