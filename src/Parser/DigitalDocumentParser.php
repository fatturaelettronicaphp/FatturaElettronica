<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use FatturaElettronicaPhp\FatturaElettronica\Decoder\DigitalDocumentDecoder;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DocumentFormat;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\CannotDecodeFile;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidFileNameExtension;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidP7MFile;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidXmlFile;
use SimpleXMLElement;
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
     * Costruttore del parser
     *
     * @param string|SimpleXMLElement $filePathOrXml
     */
    public function __construct($filePathOrXml)
    {
        if (is_object($filePathOrXml) && $filePathOrXml instanceof SimpleXMLElement) {
            $this->createFromXml($filePathOrXml);

            return;
        }

        $this->createFromFile($filePathOrXml);
    }

    public function parse(?DigitalDocumentInterface $digitalDocument = null): DigitalDocumentInterface
    {
        if ($digitalDocument === null) {
            $digitalDocument = new DigitalDocument();
        }

        $simpleXml = $this->xml();

        $headerParser    = new DigitalDocumentHeaderParser($simpleXml);
        $digitalDocument = $headerParser->parse($digitalDocument);

        foreach ($simpleXml->xpath('//FatturaElettronicaBody') as $body) {
            $bodyParser   = new DigitalDocumentBodyParser($body);
            $bodyInstance = $bodyParser->parse();
            $digitalDocument->addDigitalDocumentInstance($bodyInstance);
        }

        return $digitalDocument;
    }

    public function xml(): SimpleXMLElement
    {
        return $this->xml;
    }

    public function originalFilename(): ?string
    {
        return $this->fileName;
    }

    public function xmlFilePath(): string
    {
        return $this->xmlFilePath;
    }

    protected function createFromXml(SimpleXMLElement $xml): void
    {
        $this->xml = $xml;
    }

    protected function createFromFile(string $filePath): void
    {
        $this->fileName = $filePath;

        if (! file_exists($filePath)) {
            throw new InvalidFileNameExtension(sprintf('File does not exist "%s"', $filePath));
        }

        $simpleXml = (new DigitalDocumentDecoder())->decode($filePath);
        if (! $simpleXml) {
            throw new InvalidXmlFile($filePath);
        }

        $this->xml = $simpleXml;
    }
}
