<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use FatturaElettronicaPhp\FatturaElettronica\Address;
use FatturaElettronicaPhp\FatturaElettronica\Billable;
use FatturaElettronicaPhp\FatturaElettronica\BillablePerson;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\AddressInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\BillableInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\FundInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\RelatedDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\TotalInterface;
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
use FatturaElettronicaPhp\FatturaElettronica\Parser\XmlUtilities;
use FatturaElettronicaPhp\FatturaElettronica\RelatedDocument;
use FatturaElettronicaPhp\FatturaElettronica\Representative;
use FatturaElettronicaPhp\FatturaElettronica\Shipment;
use FatturaElettronicaPhp\FatturaElettronica\ShippingLabel;
use FatturaElettronicaPhp\FatturaElettronica\Supplier;
use DateTime;
use TypeError;
use FatturaElettronicaPhp\FatturaElettronica\Total;

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
     * @return \FatturaElettronicaPhp\FatturaElettronica\Total
     */
    protected function extractTotalFrom ($total): TotalInterface
    {
        $instance = new Total();

        $taxPercentage = $this->extractValueFromXmlElement($total, 'AliquotaIVA');
        $nature = $this->extractValueFromXmlElement($total, 'Natura');
        $type = $this->extractValueFromXmlElement($total, 'EsigibilitaIVA');
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
            ->setTaxType($type)
            ->setReference($reference);

        return $instance;
}
}
