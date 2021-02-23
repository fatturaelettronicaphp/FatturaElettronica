<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DocumentFormat;
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
     * @param string|SimpleXMLElement $filePathOrXml
     * @param string $extension
     */
    public function __construct($filePathOrXml, string $extension = '')
    {
        if (is_object($filePathOrXml) && $filePathOrXml instanceof SimpleXMLElement) {
            $this->createFromXml($filePathOrXml);

            return;
        }

        $this->createFromFile($filePathOrXml, $extension);
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

    public function xmlFilePath(): ?string
    {
        return $this->xmlFilePath;
    }

    public function p7mFilePath(): ?string
    {
        return $this->p7mFilePath;
    }

    protected function extractP7m(): void
    {
        try {
            $this->xmlFilePath = $this->extractP7mToXml($this->p7mFilePath);
        } catch (InvalidP7MFile $e) {
            $p7mTmpFilePath = tempnam(sys_get_temp_dir(), 'fattura_elettronica_') . '.xml.p7m';
            file_put_contents($p7mTmpFilePath, base64_decode(file_get_contents($this->p7mFilePath)));

            $this->xmlFilePath = $this->extractP7mToXml($p7mTmpFilePath);
        }
    }

    protected function extractP7mToXml(string $p7mFilePath): string
    {
        if (! file_exists($p7mFilePath)) {
            throw new InvalidP7MFile('File does not exist: ' . $p7mFilePath);
        }

        $xmlPath = tempnam(sys_get_temp_dir(), 'fattura_elettronica_') . '.xml';
        $pemPath = tempnam(sys_get_temp_dir(), 'fattura_elettronica_') . '.pem';

        /**
         * This is the evivalent call to
         * $exitCode = 0;
         * $output   = [];
         * exec(sprintf('openssl smime -verify -noverify -nosigs -in %s -inform DER -out %s 2> /dev/null', $p7mFilePath, $xmlPath), $output, $exitCode);
         **/
        $p7mFilePath = $this->convertFromDERtoSMIMEFormat($p7mFilePath);
        openssl_pkcs7_verify($p7mFilePath, PKCS7_NOVERIFY + PKCS7_NOSIGS, $pemPath, [__DIR__ . '/ca.pem'], __DIR__ . '/ca.pem', $xmlPath);

        return $xmlPath;
    }

    protected function convertFromDERtoSMIMEFormat(string $file): string
    {
        $pemPath = tempnam(sys_get_temp_dir(), basename($file));
        $to      = <<<TXT
MIME-Version: 1.0
Content-Disposition: attachment; filename="smime.p7m"
Content-Type: application/x-pkcs7-mime; smime-type=signed-data; name="smime.p7m"
Content-Transfer-Encoding: base64
\n
TXT;
        $from = file_get_contents($file);
        $to .= chunk_split(base64_encode($from));
        file_put_contents($pemPath, $to);

        return $pemPath;
    }

    protected function der2pem(string $p7mFilePath): string
    {
        $pemPath = tempnam(sys_get_temp_dir(), basename($p7mFilePath));
        $pem     = chunk_split(base64_encode(file_get_contents($p7mFilePath)), 64, "\n");
        $pem     = "-----BEGIN CERTIFICATE-----\n" . $pem . "-----END CERTIFICATE-----\n";

        file_put_contents($pemPath, $pem);

        return $pemPath;
    }

    protected function extractFileNameAndType(string $filePath, $extension): void
    {
        // Split extension and file name
        $extension      = strtolower($extension);
        $fileName       = pathinfo($filePath, PATHINFO_BASENAME);
        $fileNameParts  = explode(".", $fileName);
        $this->fileName = array_shift($fileNameParts);

        try {
            $this->fileType = DocumentFormat::from($extension);
            $this->filePath = $filePath;
        } catch (TypeError $e) {
            throw new InvalidFileNameExtension(sprintf('Invalid file extension "%s"', $extension));
        }
    }

    protected function createFromXml(SimpleXMLElement $xml): void
    {
        $this->xml = $xml;
    }

    protected function createFromFile(string $filePath, ?string $extension = null): void
    {
        if (! file_exists($filePath)) {
            throw new InvalidFileNameExtension(sprintf('File does not exist "%s"', $filePath));
        }

        if (empty($extension)) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        }

        $this->extractFileNameAndType($filePath, $extension);

        if ($this->fileType === DocumentFormat::P7M) {
            $this->p7mFilePath = $this->filePath;
            $this->extractP7m();
        }

        if ($this->fileType === DocumentFormat::XML) {
            $this->xmlFilePath = $this->filePath;
        }

        libxml_use_internal_errors(true);
        $simpleXml = simplexml_load_string(file_get_contents($this->xmlFilePath()), 'SimpleXMLElement', LIBXML_NOERROR + LIBXML_NOWARNING);

        if (! $simpleXml) {
            throw new InvalidXmlFile();
        }

        $this->xml = $simpleXml;
    }
}
