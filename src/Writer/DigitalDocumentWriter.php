<?php

namespace Weble\FatturaElettronica\Writer;

use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\RelatedDocumentInterface;
use Weble\FatturaElettronica\Customer;
use Weble\FatturaElettronica\Enums\TaxRegime;
use Weble\FatturaElettronica\RelatedDocument;
use Weble\FatturaElettronica\Supplier;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentWriterInterface;
use Weble\FatturaElettronica\Enums\RecipientCode;
use Weble\FatturaElettronica\Exceptions\InvalidDocument;
use Weble\FatturaElettronica\Utilities\Pipeline;
use Weble\FatturaElettronica\Utilities\SimpleXmlExtended;
use SimpleXMLElement;
use Weble\FatturaElettronica\Writer\Body\AttachmentWriter;
use Weble\FatturaElettronica\Writer\Body\GeneralDataWriter;
use Weble\FatturaElettronica\Writer\Body\PaymentsWriter;
use Weble\FatturaElettronica\Writer\Body\ProductsWriter;
use Weble\FatturaElettronica\Writer\Body\VehicleWriter;
use Weble\FatturaElettronica\Writer\Header\CustomerWriter;
use Weble\FatturaElettronica\Writer\Header\EmittingSubjectWriter;
use Weble\FatturaElettronica\Writer\Header\IntermediaryWriter;
use Weble\FatturaElettronica\Writer\Header\RepresentativeWriter;
use Weble\FatturaElettronica\Writer\Header\SupplierWriter;
use Weble\FatturaElettronica\Writer\Header\TransmissionDataWriter;

class DigitalDocumentWriter implements DigitalDocumentWriterInterface
{
    /** @var DigitalDocumentInterface */
    protected $document;

    /** @var SimpleXmlExtended */
    protected $xml;

    public function __construct (DigitalDocumentInterface $document)
    {
        $this->document = $document;
    }

    public function xml (): SimpleXMLElement
    {
        return $this->xml;
    }

    public function write ($filePath): bool
    {
        return $this->xml()->asXML($filePath);
    }


    public function generate (): DigitalDocumentWriterInterface
    {
        $this
            ->createXml()
            ->writeHeader();

        foreach ($this->document->getDocumentInstances() as $instance) {
           $this->writeBody($instance);
        }

        return $this;
    }

    protected function writeHeader (): self
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
                EmittingSubjectWriter::class
            ])
            ->thenReturn();

        return $this;
    }

    protected function writeBody (DigitalDocumentInstanceInterface $body): self
    {
        $bodyXml = $this->xml->addChild('FatturaElettronicaBody');

        $parserPipeline = new Pipeline();

        $parserPipeline
            ->send($bodyXml)
            ->with($body)
            ->usingMethod('write')
            ->through([
                GeneralDataWriter::class,
                AttachmentWriter::class,
                PaymentsWriter::class,
                ProductsWriter::class,
                VehicleWriter::class
            ])
            ->thenReturn();

        return $this;
    }

    protected function createXml (): self
    {
        $this->xml = new SimpleXmlExtended('<?xml version="1.0" encoding="UTF-8"?><p:FatturaElettronica />', LIBXML_NOERROR);

        $namespaces = [
            'xmlns:xmlns:p' => 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2',
            'xmlns:xmlns:ds' => 'http://www.w3.org/2000/09/xmldsig#',
            'xmlns:xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:xsi:schemaLocation' => 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd',
            'versione' => (string)$this->document->getTransmissionFormat()
        ];

        foreach ($namespaces as $name => $value) {
            $this->xml->addAttribute($name, $value);
        }

        return $this;
    }
}
