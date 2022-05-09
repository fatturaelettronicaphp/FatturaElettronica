<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use FatturaElettronicaPhp\FatturaElettronica\Address;
use FatturaElettronicaPhp\FatturaElettronica\BillablePerson;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\AddressInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\BillablePersonInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Parser\XmlUtilities;
use SimpleXMLElement;

abstract class AbstractBodyParser
{
    use XmlUtilities;

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /** @var DigitalDocumentInstanceInterface */
    protected $digitalDocumentInstance;

    public function __construct(DigitalDocumentInstanceInterface $digitalDocumentInstance)
    {
        $this->digitalDocumentInstance = $digitalDocumentInstance;
    }

    public function xml(): SimpleXMLElement
    {
        return $this->xml;
    }

    public function parse(SimpleXMLElement $xml): DigitalDocumentInstanceInterface
    {
        $this->xml = $xml;

        $this->performParsing();

        return $this->digitalDocumentInstance;
    }

    abstract protected function performParsing();

    protected function extractBillableInformationsFrom(SimpleXMLElement $xml, string $class = BillablePerson::class): BillablePersonInterface
    {
        $billable = new $class();
        if (! $billable instanceof BillablePersonInterface) {
            $billable = new BillablePerson();
        }

        $title = $this->extractValueFromXmlElement($xml, 'Anagrafica/Nome');
        $billable->setTitle($title);

        $customerName = $this->extractValueFromXmlElement($xml, 'Anagrafica/Nome');
        $billable->setName($customerName);

        $customerSurname = $this->extractValueFromXmlElement($xml, 'Anagrafica/Cognome');
        $billable->setSurname($customerSurname);

        $customerOrganization = $this->extractValueFromXmlElement($xml, 'Anagrafica/Denominazione');
        $billable->setOrganization($customerOrganization);

        $customerFiscalCode = $this->extractValueFromXmlElement($xml, 'CodiceFiscale');
        $billable->setFiscalCode($customerFiscalCode);

        $value = $this->extractValueFromXmlElement($xml, 'IdFiscaleIVA/IdPaese');
        $billable->setCountryCode($value);

        $customerVatNumber = $this->extractValueFromXmlElement($xml, 'IdFiscaleIVA/IdCodice');
        if ($customerVatNumber === null) {
            $customerVatNumber = '';
        }
        $billable->setVatNumber($customerVatNumber);

        return $billable;
    }

    protected function extractAddressInformationFrom(SimpleXMLElement $xml): AddressInterface
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
