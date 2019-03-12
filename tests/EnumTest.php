<?php


namespace Weble\FatturaElettronica\Tests;

use PHPUnit\Framework\TestCase;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Enums\AssociateType;
use Weble\FatturaElettronica\Enums\TransmissionFormat;
use Weble\FatturaElettronica\Parser\DigitalDocumentParser;
use Weble\FatturaElettronica\Writer\DigitalDocumentWriter;

class EnumTest extends TestCase
{
    /** @test */
    public function enum_values_and_descriptions ()
    {
        $this->assertEquals((string) AssociateType::SU(), (string) AssociateType::single());
        $this->assertEquals((string) AssociateType::SM(), (string) AssociateType::multiple());

        $this->assertEquals('Socio Unico', (string) AssociateType::single()->description);
        $this->assertEquals('Più Soci', (string) AssociateType::multiple()->description);
    }
}