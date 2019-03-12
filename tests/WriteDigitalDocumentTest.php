<?php


namespace Weble\FatturaElettronica\Tests;

use PHPUnit\Framework\TestCase;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Enums\TransmissionFormat;
use Weble\FatturaElettronica\Parser\DigitalDocumentParser;
use Weble\FatturaElettronica\Writer\DigitalDocumentWriter;

class WriteDigitalDocumentTest extends TestCase
{
    /** @test */
    public function can_write_xml_invoice_from_p7m_invoice ()
    {
        $file = dirname(__FILE__) . '/fixtures/IT00484960588_ERKHK.xml.p7m';
        $documentParser = new DigitalDocumentParser($file);

        $eDocument = $documentParser->parse();

        $writer = new DigitalDocumentWriter($eDocument);
        $xml = $writer->generate()->xml();

        $this->assertTrue($writer instanceof DigitalDocumentWriter);
        $this->assertTrue($xml instanceof \SimpleXMLElement);

        $eDocumentGenerated = (new DigitalDocumentParser($xml))->parse();
        $this->assertEquals($eDocument->toArray(), $eDocumentGenerated->toArray());
    }

    /** @test */
    public function can_write_xml_invoice_from_xml_invoice ()
    {
        $file = dirname(__FILE__) . '/fixtures/IT01234567890_FPR02.xml';
        $documentParser = new DigitalDocumentParser($file);

        $eDocument = $documentParser->parse();

        $writer = new DigitalDocumentWriter($eDocument);
        $xml = $writer->generate()->xml();

        $this->assertTrue($writer instanceof DigitalDocumentWriter);
        $this->assertTrue($xml instanceof \SimpleXMLElement);

        $eDocumentGenerated = (new DigitalDocumentParser($xml))->parse();
        $this->assertEquals($eDocument->toArray(), $eDocumentGenerated->toArray());
    }
}