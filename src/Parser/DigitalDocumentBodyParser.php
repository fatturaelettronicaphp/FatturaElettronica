<?php

namespace Weble\FatturaElettronica\Parser;

use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use Weble\FatturaElettronica\DigitalDocumentInstance;
use SimpleXMLElement;
use Weble\FatturaElettronica\Parser\Body\ConventionsParser;
use Weble\FatturaElettronica\Parser\Body\DeductionParser;
use Weble\FatturaElettronica\Parser\Body\DiscountParser;
use Weble\FatturaElettronica\Parser\Body\FundParser;
use Weble\FatturaElettronica\Parser\Body\GeneralDataParser;
use Weble\FatturaElettronica\Parser\Body\MainInvoiceParser;
use Weble\FatturaElettronica\Parser\Body\LinesParser;
use Weble\FatturaElettronica\Parser\Body\PurchaseOrderParser;
use Weble\FatturaElettronica\Parser\Body\RecepitsParser;
use Weble\FatturaElettronica\Parser\Body\RelatedInvoicesParser;
use Weble\FatturaElettronica\Parser\Body\SalParser;
use Weble\FatturaElettronica\Parser\Body\ShipmentInformationsParser;
use Weble\FatturaElettronica\Parser\Body\ShippingLabelsParser;
use Weble\FatturaElettronica\Parser\Body\SummaryParser;
use Weble\FatturaElettronica\Parser\Body\VirtualDutyParser;

class DigitalDocumentBodyParser implements DigitalDocumentParserInterface
{
    use XmlUtilities;

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /** @var DigitalDocumentInstanceInterface */
    protected $digitalDocymentInstance;

    public function __construct (SimpleXMLElement $xml)
    {
        $this->xml = $xml;
        $this->digitalDocymentInstance = new DigitalDocumentInstance();
    }

    public function xml (): SimpleXMLElement
    {
        return $this->xml;
    }

    public function parse (): DigitalDocumentInstanceInterface
    {
        $parserPipeline = new ParserPipeline();

        return $parserPipeline
            ->send($this->digitalDocymentInstance)
            ->with($this->xml())
            ->through([
                GeneralDataParser::class,
                DeductionParser::class,
                VirtualDutyParser::class,
                FundParser::class,
                DiscountParser::class,
                PurchaseOrderParser::class,
                ConventionsParser::class,
                RecepitsParser::class,
                RelatedInvoicesParser::class,
                SalParser::class,
                ShippingLabelsParser::class,
                ShipmentInformationsParser::class,
                MainInvoiceParser::class,
                LinesParser::class,
                SummaryParser::class,
            ])
            ->thenReturn();
    }
}
