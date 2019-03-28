<?php


namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use PHPUnit\Framework\TestCase;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\AttachmentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\PaymentDetailsInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\PaymentInfoInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\TotalInterface;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\Enums\TransmissionFormat;
use FatturaElettronicaPhp\FatturaElettronica\Parser\DigitalDocumentParser;

class SameXmlTest extends TestCase
{

    /** @test */
    public function can_read_xml_invoice()
    {
        $file = dirname(__FILE__) . '/fixtures/IT01234567890_FPR02.xml';
        $xml = simplexml_load_file($file);
        $eDocument = DigitalDocument::parseFrom($xml);

        $this->assertEquals($eDocument, DigitalDocument::parseFrom($eDocument->serialize()));
        $this->assertTrue($eDocument->isValid());
    }
}