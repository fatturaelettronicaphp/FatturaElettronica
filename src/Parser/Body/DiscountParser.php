<?php

namespace Weble\FatturaElettronica\Parser\Body;

use Weble\FatturaElettronica\Address;
use Weble\FatturaElettronica\Billable;
use Weble\FatturaElettronica\BillablePerson;
use Weble\FatturaElettronica\Contracts\AddressInterface;
use Weble\FatturaElettronica\Contracts\BillableInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use Weble\FatturaElettronica\Contracts\DiscountInterface;
use Weble\FatturaElettronica\Contracts\FundInterface;
use Weble\FatturaElettronica\Contracts\RelatedDocumentInterface;
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
use Weble\FatturaElettronica\Parser\XmlUtilities;
use Weble\FatturaElettronica\RelatedDocument;
use Weble\FatturaElettronica\Representative;
use Weble\FatturaElettronica\Shipment;
use Weble\FatturaElettronica\ShippingLabel;
use Weble\FatturaElettronica\Supplier;
use DateTime;
use TypeError;

class DiscountParser extends AbstractBodyParser
{
    protected function performParsing ()
    {
        $discounts = (array)$this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/ScontoMaggiorazione', false);
        foreach ($discounts as $discount) {
            $discountInstance = $this->extractDiscountInformationsFrom($discount);
            $this->digitalDocymentInstance->addDiscount($discountInstance);
        }
    }

    protected function extractDiscountInformationsFrom ($discount): DiscountInterface
    {
        $discountInstance = new Discount();

        $value = $this->extractValueFromXmlElement($discount, 'Tipo');
        $discountInstance->setType($value);

        $value = $this->extractValueFromXmlElement($discount, 'Percentuale');
        $discountInstance->setPercentage($value);

        $value = $this->extractValueFromXmlElement($discount, 'Importo');
        $discountInstance->setAmount($value);

        return $discountInstance;
    }
}
