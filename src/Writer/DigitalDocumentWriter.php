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
use Weble\FatturaElettronica\Utilities\SimpleXmlExtended;
use SimpleXMLElement;

class DigitalDocumentWriter implements DigitalDocumentWriterInterface
{
    /** @var DigitalDocumentInterface */
    protected $document;

    /** @var SimpleXmlExtended */
    protected $xml;

    public function __construct (DigitalDocumentInterface $document)
    {
        $this->document = $document;

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
    }

    public function xml (): SimpleXMLElement
    {
        return $this->xml;
    }

    public function write ($filePath): bool
    {
        return true;
    }


    public function generate (): DigitalDocumentWriterInterface
    {
        $headerWriter = new DigitalDocumentHeaderWriter($this->document, $this->xml);
        $this->xml = $headerWriter->generate()->xml();

        foreach ($this->document->getDocumentInstances() as $instance) {
            $bodyWriter = new DigitalDocumentBodyWriter($instance, $this->xml);
            $this->xml = $bodyWriter->generate()->xml();
        }

        return $this;
    }
}
