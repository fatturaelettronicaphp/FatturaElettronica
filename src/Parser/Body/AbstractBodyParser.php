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

abstract class AbstractBodyParser
{
    use XmlUtilities;

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /** @var DigitalDocumentInstanceInterface */
    protected $digitalDocymentInstance;

    public function __construct (DigitalDocumentInstanceInterface $digitalDocumentInstance)
    {
        $this->digitalDocymentInstance = $digitalDocumentInstance;
    }

    public function xml (): SimpleXMLElement
    {
        return $this->xml;
    }

    public function parse (SimpleXMLElement $xml): DigitalDocumentInstanceInterface
    {
        $this->xml = $xml;

        $this->performParsing();

        return $this->digitalDocymentInstance;
    }

    abstract protected function performParsing();

    protected function extractBillableInformationsFrom (SimpleXMLElement $xml): BillableInterface
    {
        $billable = new BillablePerson();

        $title = $this->extractValueFromXmlElement($xml, 'Anagrafica/Nome');
        $billable->setTitle($title);

        $customerName = $this->extractValueFromXmlElement($xml,'Anagrafica/Nome');
        $billable->setName($customerName);

        $customerSurname = $this->extractValueFromXmlElement($xml,'Anagrafica/Cognome');
        $billable->setSurname($customerSurname);

        $customerOrganization = $this->extractValueFromXmlElement($xml,'Anagrafica/Denominazione');
        $billable->setOrganization($customerOrganization);

        $customerFiscalCode = $this->extractValueFromXmlElement($xml,'CodiceFiscale');
        $billable->setFiscalCode($customerFiscalCode);

        $value = $this->extractValueFromXmlElement($xml,'IdFiscaleIVA/IdPaese');
        $billable->setCountryCode($value);

        $customerVatNumber = $this->extractValueFromXmlElement($xml,'IdFiscaleIVA/IdCodice');
        if ($customerVatNumber === null) {
            $customerVatNumber = '';
        }
        $billable->setVatNumber($customerVatNumber);

        return $billable;
    }

    protected function extractAddressInformationFrom (SimpleXMLElement $xml): AddressInterface
    {
        $address = new Address();

        $value = $this->extractValueFromXmlElement($xml, 'Indirizzo');
        $address->setStreet($value);

        $value = $this->extractValueFromXmlElement($xml, 'NumeroCivico');
        $address->setStreetNumber($value);

        $value = $this->extractValueFromXmlElement($xml, 'CAP');
        $address->setZip($value);

        $value = $this->extractValueFromXmlElement($xml, 'Comune');
        $address->setCity($value);

        $value = $this->extractValueFromXmlElement($xml, 'Provincia');
        $address->setState($value);

        $value = $this->extractValueFromXmlElement($xml, 'Nazione');
        $address->setCountryCode($value);

        return $address;
    }

}
