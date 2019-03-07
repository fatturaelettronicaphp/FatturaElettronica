<?php


namespace Weble\FatturaElettronica\Tests;

use PHPUnit\Framework\TestCase;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Enums\TransmissionFormat;
use Weble\FatturaElettronica\Parser\DigitalDocumentParser;

class ParseDigitalDocumentTest extends TestCase
{
    /** @test */
    public function can_read_p7m_invoice ()
    {
        $file = dirname(__FILE__) . '/fixtures/IT00484960588_ERKHK.xml.p7m';
        $documentParser = new DigitalDocumentParser($file);

        $eDocument = $documentParser->parse();

        dd($eDocument->toArray());

        $this->assertTrue($eDocument instanceof DigitalDocumentInterface);

        $this->assertTrue($eDocument->getTransmissionFormat()->equals(TransmissionFormat::FPR12()));

        $this->assertEquals('03579410246', $eDocument->getCustomer()->getVatNumber());
        $this->assertEquals('WEBLE S.R.L.', $eDocument->getCustomer()->getOrganization());

        $this->assertEquals('00484960588', $eDocument->getSupplier()->getFiscalCode());
        $this->assertEquals('00905811006', $eDocument->getSupplier()->getVatNumber());
        $this->assertEquals('Eni SpADivisione Refining & Marketing', $eDocument->getSupplier()->getOrganization());
    }
}