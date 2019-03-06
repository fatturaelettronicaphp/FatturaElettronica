<?php

namespace Weble\FatturaElettronica\Parser;

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
            $this->extractCustomerInformations($simpleXml)
        );

        $digitalDocument->setSupplier(
            $this->extractSupplierInformations($simpleXml)
        );

        foreach ($simpleXml->xpath('//FatturaElettronicaBody') as $body) {
            $edocumentBody = $this->extractRowInformations($body);
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
        libxml_use_internal_errors(true);
        $simpleXml = simplexml_load_string(file_get_contents($this->xmlFilePath()), 'SimpleXMLElement', LIBXML_NOERROR + LIBXML_NOWARNING);

        if (!$simpleXml) {
            throw new InvalidXmlFile();
        }

        return $simpleXml;
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

    public function extractCustomerInformations (SimpleXMLElement $simpleXml): BillableInterface
    {
        $customer = new Customer();

        /* Cliente - Nome */
        $customerName = $simpleXml->xpath('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica/Nome');
        if (!empty($customerName) and isset($customerName[0])) {
            $customer->setName($customerName[0]->__toString());
        }

        /* Cliente - Cognome */
        $customerSurname = $simpleXml->xpath('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica/Cognome');
        if (!empty($customerSurname) and isset($customerSurname[0])) {
            $customer->setSurname($customerSurname[0]->__toString());
        }

        /* Cliente - Azienda */
        $customerOrganization = $simpleXml->xpath('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica/Denominazione');
        if (!empty($customerOrganization) and isset($customerOrganization[0])) {
            $customer->setOrganization($customerOrganization[0]->__toString());
        }

        /* Cliente - Codice fiscale */
        $customerFiscalCode = $simpleXml->xpath('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/CodiceFiscale');
        if (empty($customerFiscalCode) or !isset($customerFiscalCode[0])) {
            $customerFiscalCode = '';

        } else {
            $customerFiscalCode = $customerFiscalCode[0]->__toString();
        }

        $customer->setFiscalCode($customerFiscalCode);

        /* Cliente - Partita iva */
        $customerVatNumber = $simpleXml->xpath('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/IdFiscaleIVA/IdCodice');
        if (empty($customerVatNumber) or !isset($customerVatNumber[0])) {
            $customerVatNumber = '';

        } else {
            $customerVatNumber = $customerVatNumber[0]->__toString();
        }

        $customer->setVatNumber($customerVatNumber);

        return $customer;
    }

    public function extractSupplierInformations (SimpleXMLElement $simpleXml): BillableInterface
    {
        $supplier = new Supplier();

        /* nome emittente */
        $documentName = $simpleXml->xpath('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Nome');
        if (!empty($documentName) and isset($documentName[0])) {
            $supplier->setName($documentName[0]->__toString());
        }

        /* cognome emittente */
        $documentSurname = $simpleXml->xpath('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Cognome');
        if (!empty($documentSurname) and isset($documentSurname[0])) {
            $supplier->setSurname($documentSurname[0]->__toString());
        }

        /* ragione sociale emittente */
        $documentOrganization = $simpleXml->xpath('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Denominazione');
        if (!empty($documentOrganization) and isset($documentOrganization[0])) {
            $supplier->setOrganization($documentOrganization[0]->__toString());
        }

        /* codice fiscal emittente */
        $documentFiscalCode = $simpleXml->xpath('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/CodiceFiscale');
        if (!empty($documentFiscalCode) and isset($documentFiscalCode[0])) {
            $supplier->setFiscalCode($documentFiscalCode[0]->__toString());
        }

        /* partita iva emittente */
        $documentVatNumber = $simpleXml->xpath('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdCodice');
        if (empty($documentVatNumber) or !isset($documentVatNumber[0])) {
            throw new InvalidXmlFile('Edocument not found vat number');
        }

        $supplier->setVatNumber($documentVatNumber[0]->__toString());

        return $supplier;
    }

    protected function extractRowInformations (SimpleXMLElement $body): DigitalDocumentInstanceInterface
    {
        $types = $body->xpath('DatiGenerali/DatiGeneraliDocumento/TipoDocumento');
        if (empty($types) or !isset($types[0])) {
            throw new InvalidXmlFile('<TipoDocumento> not found');
        }
        $type = DocumentType::from($types[0]->__toString());

        $datas = $body->xpath('DatiGenerali/DatiGeneraliDocumento/Data');
        if (empty($datas) or !isset($datas[0])) {
            throw new InvalidXmlFile('<Data> not found');
        }
        $data = new DateTime($datas[0]->__toString());

        $numbers = $body->xpath('DatiGenerali/DatiGeneraliDocumento/Numero');
        if (empty($numbers) or !isset($numbers[0])) {
            throw new InvalidXmlFile('<Numero> not found');
        }
        $number = $numbers[0]->__toString();

        $documentTotals = $body->xpath('DatiGenerali/DatiGeneraliDocumento/ImportoTotaleDocumento');
        if (!empty($documentTotals) and isset($documentTotals[0])) {
            $documentTotal = $documentTotals[0]->__toString();

        } else {
            $documentTotal = null;
        }

        $totals = $body->xpath('DatiBeniServizi/DatiRiepilogo');
        $amount = 0;
        $amountTax = 0;
        foreach ($totals as $total) {
            $totalAmounts = $total->xpath('ImponibileImporto');
            $totalAmountTaxs = $total->xpath('Imposta');

            if (empty($totalAmounts) or !isset($totalAmounts[0])) {
                throw new InvalidXmlFile('<ImponibileImporto> not found');
            }

            if (empty($totalAmountTaxs) or !isset($totalAmountTaxs[0])) {
                throw new InvalidXmlFile('<Imposta> not found');
            }

            $amount += $totalAmounts[0]->__toString();
            $amountTax += $totalAmountTaxs[0]->__toString();
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
        /* Cliente - Formato trasmissione */
        $transmissionFormat = $simpleXml->xpath('//FatturaElettronicaHeader/DatiTrasmissione/FormatoTrasmissione');
        if (empty($transmissionFormat) or !isset($transmissionFormat[0])) {
            throw new InvalidXmlFile('Edocument transmission format not found');
        }

        $digitalDocument->setTransmissionFormat($transmissionFormat[0]->__toString());
    }
}
