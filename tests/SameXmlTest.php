<?php


namespace Weble\FatturaElettronica\Tests;

use PHPUnit\Framework\TestCase;
use Weble\FatturaElettronica\Contracts\AttachmentInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Contracts\DiscountInterface;
use Weble\FatturaElettronica\Contracts\PaymentDetailsInterface;
use Weble\FatturaElettronica\Contracts\PaymentInfoInterface;
use Weble\FatturaElettronica\Contracts\TotalInterface;
use Weble\FatturaElettronica\DigitalDocument;
use Weble\FatturaElettronica\Enums\TransmissionFormat;
use Weble\FatturaElettronica\Parser\DigitalDocumentParser;

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