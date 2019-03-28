<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser;

use FatturaElettronicaPhp\FatturaElettronica\Address;
use FatturaElettronicaPhp\FatturaElettronica\Billable;
use FatturaElettronicaPhp\FatturaElettronica\BillablePerson;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\AddressInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\BillableInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\CustomerInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\FundInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\RelatedDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\SupplierInterface;
use FatturaElettronicaPhp\FatturaElettronica\Customer;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocumentInstance;
use FatturaElettronicaPhp\FatturaElettronica\Discount;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DocumentFormat;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DocumentType;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidFileNameExtension;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidP7MFile;
use SimpleXMLElement;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidXmlFile;
use FatturaElettronicaPhp\FatturaElettronica\Fund;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\CustomerParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\EmittingSubjectParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\IntermediaryParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\RepresentativeParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\SupplierParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Header\TransmissionDataParser;
use FatturaElettronicaPhp\FatturaElettronica\RelatedDocument;
use FatturaElettronicaPhp\FatturaElettronica\Representative;
use FatturaElettronicaPhp\FatturaElettronica\Shipment;
use FatturaElettronicaPhp\FatturaElettronica\ShippingLabel;
use FatturaElettronicaPhp\FatturaElettronica\Supplier;
use DateTime;
use TypeError;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Pipeline;

class DigitalDocumentHeaderParser implements DigitalDocumentParserInterface
{
    use XmlUtilities;

    /** @var \SimpleXMLElement  */
    protected $xml;

    public function __construct (SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    public function parse (DigitalDocumentInterface $digitalDocument = null): DigitalDocumentInterface
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

    public function xml (): SimpleXMLElement
    {
            return $this->xml;
    }
}
