<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocumentInstance;
use SimpleXMLElement;

abstract class AbstractDigitalDocumentBodyParser
{
    use XmlUtilities;

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /** @var DigitalDocumentInstanceInterface */
    protected $digitalDocumentInstance;

    public function __construct(SimpleXMLElement $xml)
    {
        $this->xml = $xml;
        $this->digitalDocumentInstance = new DigitalDocumentInstance();
    }

    public function xml(): SimpleXMLElement
    {
        return $this->xml;
    }
}
