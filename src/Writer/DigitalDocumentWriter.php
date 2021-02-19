<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentWriterInterface;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Pipeline;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\SimpleXmlExtended;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Body\AttachmentWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Body\GeneralDataWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Body\PaymentsWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Body\ProductsWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Body\VehicleWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Header\CustomerWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Header\EmittingSubjectWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Header\IntermediaryWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Header\RepresentativeWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Header\SupplierWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Header\TransmissionDataWriter;
use SimpleXMLElement;

class DigitalDocumentWriter implements DigitalDocumentWriterInterface
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

    public function write($filePath): bool
    {
        if (stripos($filePath, '.xml') === false) {
            $filePath = $filePath . '/' . $this->document->generatedFilename();
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
                SupplierWriter::class,
                RepresentativeWriter::class,
                CustomerWriter::class,
                IntermediaryWriter::class,
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
                ProductsWriter::class,
                VehicleWriter::class,
                PaymentsWriter::class,
                AttachmentWriter::class,
            ])
            ->thenReturn();

        return $this;
    }

    protected function createXml(): self
    {
        $this->xml = new SimpleXmlExtended(
            '<?xml version="1.0" encoding="UTF-8"?><p:FatturaElettronica />',
            LIBXML_NOERROR
        );

        $namespaces = [
            'versione'               => (string)$this->document->getTransmissionFormat(),
            'xmlns:xmlns:ds'         => 'http://www.w3.org/2000/09/xmldsig#',
            'xmlns:xmlns:p'          => 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2',
            'xmlns:xmlns:xsi'        => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:xsi:schemaLocation' => 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd',
        ];

        foreach ($namespaces as $name => $value) {
            $this->xml->addAttribute($name, $value);
        }

        return $this;
    }
}
