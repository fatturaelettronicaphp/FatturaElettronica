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
use Weble\FatturaElettronica\Contracts\TotalInterface;
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
use Weble\FatturaElettronica\Total;

class SummaryParser extends AbstractBodyParser
{
    protected function performParsing ()
    {
        $totals = (array) $this->extractValueFromXml('DatiBeniServizi/DatiRiepilogo', false);
        $amount = 0;
        $amountTax = 0;

        foreach ($totals as $totalValue) {
            $total = $this->extractTotalFrom($totalValue);
            $this->digitalDocymentInstance->addTotal($total);
        }


    }

    /**
     * @param $total
     *
     * @return \Weble\FatturaElettronica\Total
     */
    protected function extractTotalFrom ($total): TotalInterface
    {
        $instance = new Total();

        $taxPercentage = $this->extractValueFromXmlElement($total, 'AliquotaIVA');
        $nature = $this->extractValueFromXmlElement($total, 'Natura');
        $expenses = $this->extractValueFromXmlElement($total, 'SpeseAccessorie');
        $rounding = $this->extractValueFromXmlElement($total, 'Arrotondamento');
        $totalValue = $this->extractValueFromXmlElement($total, 'ImponibileImporto');
        $tax = $this->extractValueFromXmlElement($total, 'Imposta');
        $reference = $this->extractValueFromXmlElement($total, 'RiferimentoNormativo');

        if ($totalValue === null) {
            throw new InvalidXmlFile('<ImponibileImporto> not found');
        }

        if ($tax === null) {
            throw new InvalidXmlFile('<Imposta> not found');
        }

        $instance
            ->setTaxAmount($tax)
            ->setTaxPercentage($taxPercentage)
            ->setVatNature($nature)
            ->setOtherExpenses($expenses)
            ->setRounding($rounding)
            ->setTotal($totalValue)
            ->setReference($reference);

        return $instance;
}
}
