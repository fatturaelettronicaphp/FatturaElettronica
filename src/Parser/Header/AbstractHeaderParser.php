<?php

namespace Weble\FatturaElettronica\Parser\Header;

use Weble\FatturaElettronica\Address;
use Weble\FatturaElettronica\BillablePerson;
use Weble\FatturaElettronica\Contracts\AddressInterface;
use Weble\FatturaElettronica\Contracts\BillableInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use SimpleXMLElement;
use Weble\FatturaElettronica\Parser\XmlUtilities;


abstract class AbstractHeaderParser
{
    use XmlUtilities;

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /** @var DigitalDocumentInterface */
    protected $document;

    public function __construct (DigitalDocumentInterface $document)
    {
        $this->document = $document;
    }

    public function xml (): SimpleXMLElement
    {
        return $this->xml;
    }

    public function parse (SimpleXMLElement $xml): DigitalDocumentInterface
    {
        $this->xml = $xml;

        $this->performParsing();

        return $this->document;
    }

    abstract protected function performParsing ();

    protected function extractBillableInformationsFrom (SimpleXMLElement $xml, BillableInterface $billable = null): BillableInterface
    {
        if ($billable === null) {
            $billable = new BillablePerson();
        }

        $title = $this->extractValueFromXmlElement($xml, 'Anagrafica/Nome');
        $billable->setTitle($title);

        $customerName = $this->extractValueFromXml('Anagrafica/Nome');
        $billable->setName($customerName);

        $customerSurname = $this->extractValueFromXml('Anagrafica/Cognome');
        $billable->setSurname($customerSurname);

        $customerOrganization = $this->extractValueFromXml('Anagrafica/Denominazione');
        $billable->setOrganization($customerOrganization);

        $customerFiscalCode = $this->extractValueFromXml('CodiceFiscale');
        $billable->setFiscalCode($customerFiscalCode);

        $value = $this->extractValueFromXml('IdFiscaleIVA/IdPaese');
        $billable->setCountryCode($value);

        $customerVatNumber = $this->extractValueFromXml('IdFiscaleIVA/IdCodice');
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
