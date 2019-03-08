<?php

namespace Weble\FatturaElettronica\Parser;

use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use Weble\FatturaElettronica\DigitalDocumentInstance;
use SimpleXMLElement;
use Weble\FatturaElettronica\Parser\Body\GeneralDataParser;

class DigitalDocumentBodyParser implements DigitalDocumentParserInterface
{
    use XmlUtilities;

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /** @var DigitalDocumentInstanceInterface */
    protected $digitalDocymentInstance;

    public function __construct (SimpleXMLElement $xml)
    {
        $this->xml = $xml;
        $this->digitalDocymentInstance = new DigitalDocumentInstance();
    }

    public function xml (): SimpleXMLElement
    {
        return $this->xml;
    }

    public function parse (): DigitalDocumentInstanceInterface
    {
        $parserPipeline = new ParserPipeline();

        return $parserPipeline
            ->send($this->digitalDocymentInstance)
            ->with($this->xml())
            ->through([
                GeneralDataParser::class,
            ])
            ->thenReturn();
    }
}
