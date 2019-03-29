<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser;

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
use FatturaElettronicaPhp\FatturaElettronica\RelatedDocument;
use FatturaElettronicaPhp\FatturaElettronica\Representative;
use FatturaElettronicaPhp\FatturaElettronica\Shipment;
use FatturaElettronicaPhp\FatturaElettronica\ShippingLabel;
use FatturaElettronicaPhp\FatturaElettronica\Supplier;
use DateTime;
use TypeError;

class DigitalDocumentParser implements DigitalDocumentParserInterface
{
    use XmlUtilities;

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
     * @var DocumentFormat
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
        if (is_object($filePath) && $filePath instanceof SimpleXMLElement) {
            $this->createFromXml($filePath);
            return;
        }

        $this->createFromFile($filePath, $extension);
    }

    public function parse (DigitalDocumentInterface $digitalDocument = null): DigitalDocumentInterface
    {
        if ($digitalDocument === null) {
            $digitalDocument = new DigitalDocument();
        }

        $simpleXml = $this->xml();

        $headerParser = new DigitalDocumentHeaderParser($simpleXml);
        $digitalDocument = $headerParser->parse($digitalDocument);

        foreach ($simpleXml->xpath('//FatturaElettronicaBody') as $body) {
            $bodyParser = new DigitalDocumentBodyParser($body);
            $bodyInstance = $bodyParser->parse();
            $digitalDocument->addDigitalDocumentInstance($bodyInstance);
        }

        return $digitalDocument;
    }

    public function xml (): SimpleXMLElement
    {
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

    protected function extractP7m (): void
    {
        try {
            $this->xmlFilePath = $this->extractP7mToXml($this->p7mFilePath);
        } catch (InvalidP7MFile $e) {
            $p7mTmpFilePath = tempnam(sys_get_temp_dir(), 'fattura_elettronica_') . '.xml.p7m';
            file_put_contents($p7mTmpFilePath, base64_decode(file_get_contents($this->p7mFilePath)));

            $this->xmlFilePath = $this->extractP7mToXml($p7mTmpFilePath);
        }
    }

    protected function extractP7mToXml ($p7mFilePath): string
    {
        if (!file_exists($p7mFilePath)) {
            throw new InvalidP7MFile('File does not exist: ' .  $p7mFilePath);
        }

        $xmlPath = tempnam(sys_get_temp_dir(), 'fattura_elettronica_') . '.xml';

        $output = [];
        $exitCode = 0;
        exec(sprintf('openssl smime -verify -noverify -nosigs -in %s -inform DER -out %s 2> /dev/null', $p7mFilePath, $xmlPath), $output, $exitCode);

        if ($exitCode !== 0) {
            throw new InvalidP7MFile('Invalid p7m file opening for file ' . $p7mFilePath);
        }

        return $xmlPath;
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

    protected function createFromXml (SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    protected function createFromFile ($filePath, $extension = null)
    {
        if (!file_exists($filePath)) {
            throw new InvalidFileNameExtension(sprintf('File does not exist "%s"', $filePath));
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

        libxml_use_internal_errors(true);
        $simpleXml = simplexml_load_string(file_get_contents($this->xmlFilePath()), 'SimpleXMLElement', LIBXML_NOERROR + LIBXML_NOWARNING);

        if (!$simpleXml) {
            throw new InvalidXmlFile();
        }

        $this->xml = $simpleXml;
    }
}
