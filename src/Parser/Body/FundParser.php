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

class FundParser extends AbstractBodyParser
{
    protected function performParsing ()
    {
        $funds = (array)$this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiCassaPrevidenziale', false);

        foreach ($funds as $fund) {
            $fundInstance = $this->extractFundInformationsFrom($fund);
            $this->digitalDocymentInstance->addFund($fundInstance);
        }
    }

    protected function extractFundInformationsFrom ($fund): FundInterface
    {
        $fundInstance = new Fund();
        $value = $this->extractValueFromXmlElement($fund, 'TipoCassa');
        $fundInstance->setType($value);

        $value = $this->extractValueFromXmlElement($fund, 'AlCassa');
        $fundInstance->setPercentage($value);

        $value = $this->extractValueFromXmlElement($fund, 'ImportoContributoCassa');
        $fundInstance->setAmount($value);

        $value = $this->extractValueFromXmlElement($fund, 'ImponibileCassa');
        $fundInstance->setSubtotal($value);

        $value = $this->extractValueFromXmlElement($fund, 'AliquotaIVA');
        $fundInstance->setTaxPercentage($value);

        $value = $this->extractValueFromXmlElement($fund, 'Ritenuta');
        $fundInstance->setDeduction($value);

        $value = $this->extractValueFromXmlElement($fund, 'Natura');
        $fundInstance->setVatNature($value);

        $value = $this->extractValueFromXmlElement($fund, 'RiferimentoAmministrazione');
        $fundInstance->setRepresentative($value);

        return $fundInstance;
    }
}
