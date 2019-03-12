<?php

namespace Weble\FatturaElettronica\Parser;

use Weble\FatturaElettronica\Address;
use Weble\FatturaElettronica\Billable;
use Weble\FatturaElettronica\BillablePerson;
use Weble\FatturaElettronica\Contracts\AddressInterface;
use Weble\FatturaElettronica\Contracts\BillableInterface;
use Weble\FatturaElettronica\Contracts\CustomerInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use Weble\FatturaElettronica\Contracts\DiscountInterface;
use Weble\FatturaElettronica\Contracts\FundInterface;
use Weble\FatturaElettronica\Contracts\RelatedDocumentInterface;
use Weble\FatturaElettronica\Contracts\SupplierInterface;
use Weble\FatturaElettronica\Customer;
use Weble\FatturaElettronica\DigitalDocument;
use Weble\FatturaElettronica\DigitalDocumentInstance;
use Weble\FatturaElettronica\Discount;
use Weble\FatturaElettronica\Enums\DocumentFormat;
use Weble\FatturaElettronica\Enums\DocumentType;
use Weble\FatturaElettronica\Exceptions\InvalidFileNameExtension;
use Weble\FatturaElettronica\Exceptions\InvalidP7MFile;
use SimpleXMLElement;
use Weble\FatturaElettronica\Exceptions\InvalidXmlFile;
use Weble\FatturaElettronica\Fund;
use Weble\FatturaElettronica\Parser\Header\CustomerParser;
use Weble\FatturaElettronica\Parser\Header\EmittingSubjectParser;
use Weble\FatturaElettronica\Parser\Header\IntermediaryParser;
use Weble\FatturaElettronica\Parser\Header\RepresentativeParser;
use Weble\FatturaElettronica\Parser\Header\SupplierParser;
use Weble\FatturaElettronica\Parser\Header\TransmissionDataParser;
use Weble\FatturaElettronica\RelatedDocument;
use Weble\FatturaElettronica\Representative;
use Weble\FatturaElettronica\Shipment;
use Weble\FatturaElettronica\ShippingLabel;
use Weble\FatturaElettronica\Supplier;
use DateTime;
use TypeError;
use Weble\FatturaElettronica\Utilities\Pipeline;

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
