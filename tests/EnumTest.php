<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use FatturaElettronicaPhp\FatturaElettronica\Enums\AssociateType;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    /** @test */
    public function enum_values_and_descriptions()
    {
        $this->assertEquals((string) AssociateType::SU(), (string) AssociateType::single());
        $this->assertEquals((string) AssociateType::SM(), (string) AssociateType::multiple());

        $this->assertEquals('Socio Unico', (string) AssociateType::single()->description);
        $this->assertEquals('Più Soci', (string) AssociateType::multiple()->description);
    }
}
