<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use FatturaElettronicaPhp\FatturaElettronica\Attachment;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocumentInstance;
use FatturaElettronicaPhp\FatturaElettronica\Parser\DigitalDocumentParser;
use PHPUnit\Framework\TestCase;

class WriteDigitalDocumentTest extends TestCase
{
    /** @test */
    public function can_generate_a_correct_filename(): void
    {
        $file = __DIR__ . '/fixtures/IT01234567890_FPR02.xml';
        $eDocument = DigitalDocument::parseFrom($file);

        $xml = $eDocument->serialize();

        $eDocumentGenerated = (new DigitalDocumentParser($xml))->parse();
        $this->assertEquals("IT01234567890_00001.xml", $eDocumentGenerated->generatedFilename());
    }

    /** @test */
    public function can_write_xml_invoice_from_p7m_invoice(): void
    {
        $file = __DIR__ . '/fixtures/IT00484960588_ERKHK.xml.p7m';
        $eDocument = DigitalDocument::parseFrom($file);

        $xml = $eDocument->serialize();

        $eDocumentGenerated = (new DigitalDocumentParser($xml))->parse();
        $this->assertEquals($eDocument->toArray(), $eDocumentGenerated->toArray());
    }

    /** @test */
    public function can_write_xml_invoice_from_xml_invoice(): void
    {
        $file = __DIR__ . '/fixtures/IT01234567890_FPR02.xml';
        $eDocument = DigitalDocument::parseFrom($file);

        $xml = $eDocument->serialize();

        $eDocumentGenerated = (new DigitalDocumentParser($xml))->parse();
        $this->assertEquals($eDocument->toArray(), $eDocumentGenerated->toArray());
    }

    /** @test */
    public function can_write_xml_file_invoice_from_xml_invoice(): void
    {
        $file = __DIR__ . '/fixtures/IT01234567890_FPR02.xml';
        $eDocument = DigitalDocument::parseFrom($file);

        $readFile = tempnam(sys_get_temp_dir(), 'fattura_elettronica') . '.xml';
        $eDocument->write($readFile);
        $xml = simplexml_load_file($readFile);

        $eDocumentGenerated = DigitalDocument::parseFrom($xml);
        $this->assertEquals($eDocument->toArray(), $eDocumentGenerated->toArray());
    }

    /** @test */
    public function can_write_xml_file_invoice_from_xml_invoice_using_generated_filename(): void
    {
        $file = __DIR__ . '/fixtures/IT01234567890_FPR02.xml';
        $eDocument = DigitalDocument::parseFrom($file);

        $readFile = tempnam(sys_get_temp_dir(), 'fattura_elettronica') . '.xml';
        $eDocument->write($readFile);

        $this->assertTrue(file_exists($readFile));

        $readFile = dirname(tempnam(sys_get_temp_dir(), 'fattura_elettronica') . '.xml');
        $eDocument->write($readFile);

        $this->assertTrue(file_exists($readFile . '/' . $eDocument->generatedFilename()));

        $readFile = dirname(tempnam(sys_get_temp_dir(), 'fattura_elettronica') . '.xml') . '/';
        $eDocument->write($readFile);

        $this->assertTrue(file_exists($readFile . '/' . $eDocument->generatedFilename()));
    }

    /** @test */
    public function can_write_document_with_empty_attachment(): void
    {
        $file = __DIR__ . '/fixtures/IT01234567890_Attachment.xml';
        $eDocument = DigitalDocument::parseFrom($file);

        /** @var DigitalDocumentInstance $instance */
        $instance = $eDocument->getDocumentInstances()[0];
        $instance->addAttachment(
            (new Attachment())
            ->setName('Test')
        );

        $readFile = dirname(tempnam(sys_get_temp_dir(), 'fattura_elettronica') . '.xml') . '/';
        $eDocument->write($readFile);

        $this->assertTrue(file_exists($readFile . '/' . $eDocument->generatedFilename()));
        $this->assertStringContainsString("<Attachment/>", file_get_contents($readFile . '/' . $eDocument->generatedFilename()));
    }
}
