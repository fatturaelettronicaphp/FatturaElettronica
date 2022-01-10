<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer;

use DOMDocument;
use Exception;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentWriterInterface;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Pipeline;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\SimpleXmlExtended;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Body\AttachmentWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Body\GeneralDataWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Body\SimplifiedProductsWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Header\EmittingSubjectWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Header\SimplifiedCustomerWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Header\SimplifiedSupplierWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Header\TransmissionDataWriter;
use SimpleXMLElement;
use Stringable;

class SimplifiedDigitalDocumentWriter implements DigitalDocumentWriterInterface
{
    /** @var DigitalDocumentInterface */
    protected $document;

    /** @var SimpleXmlExtended */
    protected $xml;

    public function __construct(DigitalDocumentInterface $document)
    {
        $this->document = $document;
    }

    public function xml(): SimpleXMLElement
    {
        return $this->xml;
    }

    /**
     * TODO: In the next major version, add a string type
     * @param string $filePath
     * @param bool $format
     * @return bool
     * @throws Exception
     */
    public function write($filePath, bool $format = false): bool
    {
        if (!is_string($filePath) && !$filePath instanceof Stringable) {
            throw new Exception('File path needs to be a string');
        }

        if (stripos($filePath, '.xml') === false) {
            $filePath = $filePath . '/' . $this->document->generatedFilename();
        }

        if ($format) {
            /** @var DOMDocument $dom */
            $dom = dom_import_simplexml($this->generate()->xml())->ownerDocument;
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;

            return $dom->save($filePath);
        }

        return $this->generate()->xml()->asXML($filePath);
    }

    public function generate(): DigitalDocumentWriterInterface
    {
        $this
            ->createXml()
            ->writeHeader();

        foreach ($this->document->getDocumentInstances() as $instance) {
            $this->writeBody($instance);
        }

        return $this;
    }

    protected function writeHeader(): self
    {
        $xmlHeader = $this->xml->addChild('FatturaElettronicaHeader');

        $parserPipeline = new Pipeline();

        $parserPipeline
            ->send($xmlHeader)
            ->with($this->document)
            ->usingMethod('write')
            ->through([
                TransmissionDataWriter::class,
                SimplifiedSupplierWriter::class,
                SimplifiedCustomerWriter::class,
                EmittingSubjectWriter::class,
            ])
            ->thenReturn();

        return $this;
    }

    protected function writeBody(DigitalDocumentInstanceInterface $body): self
    {
        $bodyXml = $this->xml->addChild('FatturaElettronicaBody');

        $parserPipeline = new Pipeline();

        $parserPipeline
            ->send($bodyXml)
            ->with($body)
            ->usingMethod('write')
            ->through([
                GeneralDataWriter::class,
                SimplifiedProductsWriter::class,
                AttachmentWriter::class,
            ])
            ->thenReturn();

        return $this;
    }

    protected function createXml(): self
    {
        $this->xml = new SimpleXmlExtended(
            '<?xml version="1.0" encoding="UTF-8"?><p:FatturaElettronicaSemplificata />',
            LIBXML_NOERROR
        );

        $namespaces = [
            'versione'       => (string)$this->document->getTransmissionFormat(),
            'xmlns:xmlns:ds' => 'http://www.w3.org/2000/09/xmldsig#',
            'xmlns:xmlns:p'  => 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.0',
        ];

        foreach ($namespaces as $name => $value) {
            $this->xml->addAttribute($name, $value);
        }

        return $this;
    }
}
