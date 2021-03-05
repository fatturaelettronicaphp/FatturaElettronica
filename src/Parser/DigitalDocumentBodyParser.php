<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocumentInstance;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\AttachmentParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\ConventionsParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\DeductionParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\DiscountParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\FundParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\GeneralDataParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\LinesParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\MainInvoiceParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\PaymentInfoParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\PurchaseOrderParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\RecepitsParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\RelatedInvoicesParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\SalParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\ShipmentInformationsParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\ShippingLabelsParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\SimplifiedProductsParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\SummaryParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\VehicleParser;
use FatturaElettronicaPhp\FatturaElettronica\Parser\Body\VirtualDutyParser;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Pipeline;
use SimpleXMLElement;

class DigitalDocumentBodyParser extends AbstractDigitalDocumentBodyParser implements DigitalDocumentParserInterface
{
    public function parse(): DigitalDocumentInstanceInterface
    {
        return (new Pipeline())
            ->send($this->digitalDocumentInstance)
            ->with($this->xml())
            ->usingMethod('parse')
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
                VehicleParser::class,
                PaymentInfoParser::class,
                AttachmentParser::class,
            ])
            ->thenReturn();
    }
}
