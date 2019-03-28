<?php


namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use PHPUnit\Framework\TestCase;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\AssociateType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\TransmissionFormat;
use FatturaElettronicaPhp\FatturaElettronica\Parser\DigitalDocumentParser;
use FatturaElettronicaPhp\FatturaElettronica\Writer\DigitalDocumentWriter;

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