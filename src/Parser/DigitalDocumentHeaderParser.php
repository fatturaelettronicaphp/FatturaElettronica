<?php

namespace Weble\FatturaElettronica\Parser;

use Weble\FatturaElettronica\Address;
use Weble\FatturaElettronica\Billable;
use Weble\FatturaElettronica\BillablePerson;
use Weble\FatturaElettronica\Contracts\AddressInterface;
use Weble\FatturaElettronica\Contracts\BillableInterface;
use Weble\FatturaElettronica\Contracts\CustomerInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use Weble\FatturaElettronica\Contracts\DiscountInterface;
use Weble\FatturaElettronica\Contracts\FundInterface;
use Weble\FatturaElettronica\Contracts\RelatedDocumentInterface;
use Weble\FatturaElettronica\Contracts\SupplierInterface;
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
use Weble\FatturaElettronica\RelatedDocument;
use Weble\FatturaElettronica\Representative;
use Weble\FatturaElettronica\Shipment;
use Weble\FatturaElettronica\ShippingLabel;
use Weble\FatturaElettronica\Supplier;
use DateTime;
use TypeError;

class DigitalDocumentHeaderParser implements DigitalDocumentParserInterface
{
    use XmlUtilities;

    /** @var \SimpleXMLElement  */
    protected $xml;

    public function __construct (SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    public function parse (DigitalDocumentInterface $digitalDocument = null): DigitalDocumentInterface
    {
        if ($digitalDocument === null) {
            $digitalDocument = new DigitalDocument();
        }

        $digitalDocument = $this->extractDigitalDocumentInformations($digitalDocument);

        $digitalDocument->setCustomer(
            $this->extractCustomerInformations()
        );

        $digitalDocument->setSupplier(
            $this->extractSupplierInformations()
        );

        $representative = $this->extractRepresentativeInformations();

        if ($representative !== null) {
            $digitalDocument->setRepresentative($representative);
        }

        $intermediary = $this->extractIntermediaryInformations();

        if ($intermediary !== null) {
            $digitalDocument->setIntermediary($intermediary);
        }

        return $digitalDocument;
    }

    public function xml (): SimpleXMLElement
    {
            return $this->xml;
    }

    public function extractCustomerInformations (): CustomerInterface
    {
        $customer = new Customer();

        $customerName = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica/Nome');
        $customer->setName($customerName);

        $customerSurname = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica/Cognome');
        $customer->setSurname($customerSurname);

        $customerOrganization = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica/Denominazione');
        $customer->setOrganization($customerOrganization);

        $customerFiscalCode = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/CodiceFiscale');
        $customer->setFiscalCode($customerFiscalCode);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/IdFiscaleIVA/IdPaese');
        $customer->setCountryCode($value);

        $customerVatNumber = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/IdFiscaleIVA/IdCodice');
        if ($customerVatNumber === null) {
            $customerVatNumber = '';
        }
        $customer->setVatNumber($customerVatNumber);

        $addressValue = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/Sede', false);
        if ($addressValue !== null) {
            $addressValue = array_shift($addressValue);
        }

        if ($addressValue !== null) {
            $address = $this->extractAddressInformationFrom($addressValue);
            $customer->setAddress($address);
        }

        $addressValue = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/StabileOrganizzazione', false);
        if ($addressValue !== null) {
            $addressValue = array_shift($addressValue);
        }

        if ($addressValue !== null) {
            $address = $this->extractAddressInformationFrom($addressValue);
            $customer->setForeignFixedAddress($address);
        }

        $representativeValue = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/RappresentanteFiscale', false);
        if ($representativeValue !== null) {
            $representativeValue = array_shift($representativeValue);
        }

        if ($representativeValue !== null) {
            $representative = new Representative();
            $this->extractBillableInformationsFrom($representativeValue, $representative);
            $customer->setRepresentative($representative);
        }

        return $customer;
    }

    public function extractBillableInformationsFrom (SimpleXMLElement $xml, BillableInterface $billable = null): BillableInterface
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

    public function extractIntermediaryInformations ()
    {
        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente', false);
        if ($value === null) {
            return $value;
        }

        $value = array_shift($value);
        if ($value === null) {
            return $value;
        }

        $intermediary = new BillablePerson();

        $documentName = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/Anagrafica/Nome');
        $intermediary->setName($documentName);

        $documentSurname = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/Anagrafica/Cognome');
        $intermediary->setSurname($documentSurname);

        $documentOrganization = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/Anagrafica/Denominazione');
        $intermediary->setOrganization($documentOrganization);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/Anagrafica/Titolo');
        $intermediary->setTitle($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/CodiceFiscale');
        $intermediary->setFiscalCode($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/IdFiscaleIVA/IdCodice');
        $intermediary->setVatNumber($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/TerzoIntermediarioOSoggettoEmittente/DatiAnagrafici/IdFiscaleIVA/IdPaese');
        $intermediary->setCountryCode($value);

        return $intermediary;
    }

    public function extractRepresentativeInformations ()
    {
        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale', false);
        if ($value === null) {
            return $value;
        }

        $value = array_shift($value);
        if ($value === null) {
            return $value;
        }

        $intermediary = new BillablePerson();

        $documentName = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/Anagrafica/Nome');
        $intermediary->setName($documentName);

        $documentSurname = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/Anagrafica/Cognome');
        $intermediary->setSurname($documentSurname);

        $documentOrganization = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/Anagrafica/Denominazione');
        $intermediary->setOrganization($documentOrganization);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/Anagrafica/Titolo');
        $intermediary->setTitle($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/CodiceFiscale');
        $intermediary->setFiscalCode($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/IdFiscaleIVA/IdCodice');
        $intermediary->setVatNumber($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/RappresentanteFiscale/DatiAnagrafici/IdFiscaleIVA/IdPaese');
        $intermediary->setCountryCode($value);

        return $intermediary;
    }

    public function extractSupplierInformations (): SupplierInterface
    {
        $supplier = new Supplier();

        $documentName = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Nome');
        $supplier->setName($documentName);

        $documentSurname = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Cognome');
        $supplier->setSurname($documentSurname);

        $documentOrganization = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Denominazione');
        $supplier->setOrganization($documentOrganization);

        $documentFiscalCode = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/CodiceFiscale');
        $supplier->setFiscalCode($documentFiscalCode);

        $documentVatNumber = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdCodice');
        if ($documentVatNumber === null) {
            throw new InvalidXmlFile('Vat Number is required');
        }
        $supplier->setVatNumber($documentVatNumber);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdPaese');
        if ($value === null) {
            throw new InvalidXmlFile('IdPaese is required');
        }
        $supplier->setCountryCode($value);

        $addressValue = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/Sede', false);
        if ($addressValue !== null) {
            $addressValue = array_shift($addressValue);
        }

        if ($addressValue !== null) {
            $address = $this->extractAddressInformationFrom($addressValue);
            $supplier->setAddress($address);
        }

        $addressValue = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/StabileOrganizzazione', false);
        if ($addressValue !== null) {
            $addressValue = array_shift($addressValue);
        }

        if ($addressValue !== null) {
            $address = $this->extractAddressInformationFrom($addressValue);
            $supplier->setForeignFixedAddress($address);
        }

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/AlboProfessionale');
        $supplier->setRegister($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/ProvinciaAlbo');
        $supplier->setRegisterState($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/NumeroIscrizioneAlbo');
        $supplier->setRegisterNumber($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/DataIscrizioneAlbo');
        $supplier->setRegisterDate($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/RegimeFiscale');
        $supplier->setTaxRegime($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Contatti/Telefono');
        $supplier->setPhone($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Contatti/Email');
        $supplier->setEmail($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Contatti/Fax');
        $supplier->setFax($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/RiferimentoAmministrazione');
        $supplier->setAdministrativeContact($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IscrizioneRea/Ufficio');
        $supplier->setReaOffice($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IscrizioneRea/NumeroREA');
        $supplier->setReaNumber($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IscrizioneRea/CapitaleSociale');
        $supplier->setCapital($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IscrizioneRea/SocioUnico');
        $supplier->setAssociateType($value);

        $value = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IscrizioneRea/StatoLiquidazione');
        $supplier->setSettlementType($value);

        return $supplier;
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

    protected function extractDigitalDocumentInformations (DigitalDocumentInterface $digitalDocument): DigitalDocumentInterface
    {
        $transmissionFormat = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/FormatoTrasmissione');
        if ($transmissionFormat === null) {
            throw new InvalidXmlFile('Transmission Format not found');
        }

        $digitalDocument->setTransmissionFormat($transmissionFormat);

        $countryCode = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdPaese');
        $digitalDocument->setCountryCode($countryCode);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdCodice');
        $digitalDocument->setSenderVatId($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/ProgressivoInvio');
        $digitalDocument->setSendingId($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario');
        $digitalDocument->setCustomerSdiCode($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/ContattiTrasmittente/Telefono');
        $digitalDocument->setSenderPhone($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/ContattiTrasmittente/Email');
        $digitalDocument->setSenderEmail($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/PECDestinatario');
        $digitalDocument->setCustomerPec($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/SoggettoEmittente');
        $digitalDocument->setEmittingSubject($code);

        return $digitalDocument;
    }
}
