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

class GeneralDataParser extends AbstractBodyParser
{
    protected function performParsing ()
    {
        $types = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/TipoDocumento');
        if ($types === null) {
            throw new InvalidXmlFile('<TipoDocumento> not found');
        }
        $type = DocumentType::from($types);

        $datas = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Data');
        if ($datas === null) {
            throw new InvalidXmlFile('<Data> not found');
        }
        $data = new DateTime($datas);

        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Divisa');
        if ($datas === null) {
            throw new InvalidXmlFile('<Divisa> not found');
        }

        $this->digitalDocymentInstance->setCurrency($value);

        $number = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Numero');
        if ($number === null) {
            throw new InvalidXmlFile('<Numero> not found');
        }

        $descriptions = (array)$this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Causale', false);
        foreach ($descriptions as $description) {
            $this->digitalDocymentInstance->addDescription($description);
        }

        $documentTotal = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/ImportoTotaleDocumento');
        $rounding = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Arrotondamento');
        $art73 = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Art73');

        $this->digitalDocymentInstance
            ->setDocumentType($type)
            ->setDocumentDate($data)
            ->setDocumentNumber($number)
            ->setDocumentTotal($documentTotal)
            ->setArt73($art73)
            ->setRounding($rounding);
    }
}
