<?php


namespace Weble\FatturaElettronica\Tests;

use PHPUnit\Framework\TestCase;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\DigitalDocument;
use Weble\FatturaElettronica\Enums\AssociateType;
use Weble\FatturaElettronica\Enums\TransmissionFormat;
use Weble\FatturaElettronica\Parser\DigitalDocumentParser;
use Weble\FatturaElettronica\Writer\DigitalDocumentWriter;

class WriteDigitalDocumentTest extends TestCase
{
    /** @test */
    public function can_write_xml_invoice_from_p7m_invoice ()
    {
        $file = dirname(__FILE__) . '/fixtures/IT00484960588_ERKHK.xml.p7m';
        $eDocument = DigitalDocument::parseFrom($file);

        $xml = $eDocument->serialize();

        $eDocumentGenerated = (new DigitalDocumentParser($xml))->parse();
        $this->assertEquals($eDocument->toArray(), $eDocumentGenerated->toArray());
    }

    /** @test */
    public function can_write_xml_invoice_from_xml_invoice ()
    {
        $file = dirname(__FILE__) . '/fixtures/IT01234567890_FPR02.xml';
        $eDocument = DigitalDocument::parseFrom($file);

        $xml = $eDocument->serialize();

        $eDocumentGenerated = (new DigitalDocumentParser($xml))->parse();
        $this->assertEquals($eDocument->toArray(), $eDocumentGenerated->toArray());
    }

    /** @test */
    public function can_write_xml_file_invoice_from_xml_invoice ()
    {
        $file = dirname(__FILE__) . '/fixtures/IT01234567890_FPR02.xml';
        $eDocument = DigitalDocument::parseFrom($file);

        $readFile = tempnam(sys_get_temp_dir(), 'fattura_elettronica') . '.xml';
        $eDocument->write($readFile);
        $xml = simplexml_load_file($readFile);

        $eDocumentGenerated = DigitalDocument::parseFrom($xml);
        $this->assertEquals($eDocument->toArray(), $eDocumentGenerated->toArray());
    }
}