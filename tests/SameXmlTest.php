<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use PHPUnit\Framework\TestCase;

class SameXmlTest extends TestCase
{
    /** @test */
    public function can_read_xml_invoice()
    {
        $file = __DIR__ . '/fixtures/IT01234567890_FPR02.xml';
        $xml = simplexml_load_file($file);
        $eDocument = DigitalDocument::parseFrom($xml);

        $this->assertEquals($eDocument, DigitalDocument::parseFrom($eDocument->serialize()));
        $this->assertTrue($eDocument->isValid(), json_encode($eDocument->validate()->errors()));
    }
}
