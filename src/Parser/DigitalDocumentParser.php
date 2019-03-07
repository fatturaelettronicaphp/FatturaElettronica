<?php

namespace Weble\FatturaElettronica\Parser;

use Weble\FatturaElettronica\Address;
use Weble\FatturaElettronica\Contracts\AddressInterface;
use Weble\FatturaElettronica\Contracts\BillableInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use Weble\FatturaElettronica\Customer;
use Weble\FatturaElettronica\DigitalDocument;
use Weble\FatturaElettronica\DigitalDocumentInstance;
use Weble\FatturaElettronica\Enums\DocumentFormat;
use Weble\FatturaElettronica\Enums\DocumentType;
use Weble\FatturaElettronica\Exceptions\InvalidFileNameExtension;
use Weble\FatturaElettronica\Exceptions\InvalidP7MFile;
use SimpleXMLElement;
use Weble\FatturaElettronica\Exceptions\InvalidXmlFile;
use Weble\FatturaElettronica\Supplier;
use DateTime;
use TypeError;

class DigitalDocumentParser implements DigitalDocumentParserInterface
{
    /**
     * Nome del file senza estensioni
     * @var string
     */
    protected $fileName;

    /**
     * IL path del file fornito
     * @var string
     */
    protected $filePath;

    /**
     * IL path del file xml
     * @var string
     */
    protected $xmlFilePath;

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * @var \Weble\FatturaElettronica\Enums\DocumentFormat
     */
    protected $fileType;

    /**
     * Il path del file p7m se esiste
     * @var string|null
     */
    protected $p7mFilePath;

    /**
     * Costruttore del parser
     *
     * @param string $filePath
     * @param string $extension
     */
    public function __construct ($filePath, $extension = '')
    {
        if (!file_exists($filePath)) {
            throw new InvalidFileNameExtension(sprintf('Fiel does not exist "%s"', $filePath));
        }

        if (empty($extension)) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        }

        $this->extractFileNameAndType($filePath, $extension);

        if ($this->fileType->equals(DocumentFormat::p7m())) {
            $this->p7mFilePath = $this->filePath;
            $this->extractP7m();
        }

        if ($this->fileType->equals(DocumentFormat::xml())) {
            $this->xmlFilePath = $this->filePath;
        }
    }

    public function parse (DigitalDocumentInterface $digitalDocument = null): DigitalDocumentInterface
    {
        if ($digitalDocument === null) {
            $digitalDocument = new DigitalDocument();
        }

        $simpleXml = $this->xml();

        $this->extractDigitalDocumentInformations($digitalDocument, $simpleXml);

        $digitalDocument->setCustomer(
            $this->extractCustomerInformations()
        );

        $digitalDocument->setSupplier(
            $this->extractSupplierInformations()
        );

        foreach ($simpleXml->xpath('//FatturaElettronicaBody') as $body) {
            $edocumentBody = $this->extractRowInformationsFrom($body);
            $digitalDocument->addDigitalDocumentInstance($edocumentBody);
        }

        return $digitalDocument;
    }

    protected function extractP7m (): void
    {
        try {
            $this->xmlFilePath = $this->extractP7mToXml($this->p7mFilePath);
        } catch (InvalidP7MFile $e) {
            $p7mTmpFilePath = sys_get_temp_dir() . uniqid() . '.xml.p7m';
            file_put_contents($p7mTmpFilePath, base64_decode(file_get_contents($this->p7mFilePath)));

            $this->xmlFilePath = $this->extractP7mToXml($p7mTmpFilePath);
        }
    }

    protected function extractP7mToXml ($p7mFilePath): string
    {
        $xmlPath = sys_get_temp_dir() . uniqid() . '.xml';

        $output = [];
        $exitCode = 0;
        exec(sprintf('openssl smime -verify -noverify -nosigs -in %s -inform DER -out %s', $p7mFilePath, $xmlPath), $output, $exitCode);

        if ($exitCode !== 0) {
            throw new InvalidP7MFile('Invalid p7m file opening for file ' . $this->p7mFilePath);
        }

        return $xmlPath;
    }

    public function xml (): SimpleXMLElement
    {
        if ($this->xml !== null) {
            return $this->xml;
        }

        libxml_use_internal_errors(true);
        $simpleXml = simplexml_load_string(file_get_contents($this->xmlFilePath()), 'SimpleXMLElement', LIBXML_NOERROR + LIBXML_NOWARNING);

        if (!$simpleXml) {
            throw new InvalidXmlFile();
        }

        $this->xml = $simpleXml;

        return $this->xml;
    }

    public function originalFilename ()
    {
        return $this->fileName;
    }

    public function xmlFilePath ()
    {
        return $this->xmlFilePath;
    }

    public function p7mFilePath ()
    {
        return $this->p7mFilePath;
    }

    protected function extractFileNameAndType ($filePath, $extension): void
    {
        // Split extension and file name
        $extension = strtolower($extension);
        $fileName = pathinfo($filePath, PATHINFO_BASENAME);
        $fileNameParts = explode(".", $fileName);
        $this->fileName = array_shift($fileNameParts);

        try {
            $this->fileType = DocumentFormat::from($extension);
            $this->filePath = $filePath;
        } catch (TypeError $e) {
            throw new InvalidFileNameExtension(sprintf('Invalid file extension "%s"', $extension));
        }
    }

    public function extractCustomerInformations (): BillableInterface
    {
        $customer = new Customer();

        $customerName = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica/Nome');
        $customer->setName($customerName);

        $customerSurname = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica/Cognome');
        $customer->setSurname($customerSurname);

        $customerOrganization = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica/Denominazione');
        $customer->setOrganization($customerOrganization);

        $customerFiscalCode = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/CodiceFiscale');
        if ($customerFiscalCode === null) {
            $customerFiscalCode = '';
        }
        $customer->setFiscalCode($customerFiscalCode);

        $customerVatNumber = $this->extractValueFromXml('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/IdFiscaleIVA/IdCodice');
        if ($customerVatNumber === null) {
            $customerVatNumber = '';
        }
        $customer->setVatNumber($customerVatNumber);

        return $customer;
    }

    public function extractSupplierInformations (): BillableInterface
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

        $addressValue = $this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/Sede', false);
        if ($addressValue !== null) {
            $addressValue = array_shift($addressValue);
        }

        if ($addressValue !== null) {
            $address = $this->extractAddressInformationFrom($addressValue);
            $supplier->setAddress($address);
        }

        $addressValue =$this->extractValueFromXml('//FatturaElettronicaHeader/CedentePrestatore/StabileOrganizzazione', false);
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

    protected function extractRowInformationsFrom (SimpleXMLElement $body): DigitalDocumentInstanceInterface
    {
        $types = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/TipoDocumento');
        if ($types === null) {
            throw new InvalidXmlFile('<TipoDocumento> not found');
        }
        $type = DocumentType::from($types);

        $datas = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/Data');
        if ($datas === null) {
            throw new InvalidXmlFile('<Data> not found');
        }
        $data = new DateTime($datas);

        $number = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/Numero');
        if ($number === null) {
            throw new InvalidXmlFile('<Numero> not found');
        }

        $documentTotal = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/ImportoTotaleDocumento');

        $totals = $this->extractValueFromXmlElement($body, 'DatiBeniServizi/DatiRiepilogo', false);
        $amount = 0;
        $amountTax = 0;

        foreach ($totals as $total) {
            $totalAmounts = $this->extractValueFromXmlElement($total, 'ImponibileImporto');
            $totalAmountTaxs = $this->extractValueFromXmlElement($total, 'Imposta');

            if ($totalAmounts === null) {
                throw new InvalidXmlFile('<ImponibileImporto> not found');
            }

            if ($totalAmountTaxs === null) {
                throw new InvalidXmlFile('<Imposta> not found');
            }

            $amount += $totalAmounts;
            $amountTax += $totalAmountTaxs;
        }

        $digitalDocumentInstance = new DigitalDocumentInstance();
        $digitalDocumentInstance
            ->setDocumentType($type)
            ->setDocumentDate($data)
            ->setDocumentNumber($number)
            ->setAmount($amount)
            ->setAmountTax($amountTax)
            ->setDocumentTotal($documentTotal);

        return $digitalDocumentInstance;
    }

    /**
     * @param \Weble\FatturaElettronica\Contracts\DigitalDocumentInterface $digitalDocument
     * @param \SimpleXMLElement $simpleXml
     */
    protected function extractDigitalDocumentInformations (DigitalDocumentInterface $digitalDocument, SimpleXMLElement $simpleXml): void
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
    }

    protected function extractValueFromXml (string $xPath, $convertToString = true)
    {
        return $this->extractValueFromXmlElement($this->xml(), $xPath, $convertToString);
    }

    protected function extractValueFromXmlElement (SimpleXMLElement $xml, string $xPath, $convertToString = true)
    {
        $value = $xml->xpath($xPath);

        if (empty($value)) {
            return null;
        }

        if (count($value) <= 0) {
            return null;
        }

        if ($convertToString) {
            return $value[0]->__toString();
        }

        return $value;
    }
}
