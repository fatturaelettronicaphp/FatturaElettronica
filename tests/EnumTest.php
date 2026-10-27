<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use FatturaElettronicaPhp\FatturaElettronica\Enums;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    /** @test */
    public function enum_values_and_descriptions()
    {
        $this->assertEquals('N2.2', Enums\VatNature::N2_2()->value);
        $this->assertEquals('Non soggette - altri casi', Enums\VatNature::N2_2()->description);
    }

    /** @test */
    public function extracts_enums_from_xsd()
    {
        $this->assertCount(24, Enums\VatNature::values());
        $this->assertCount(2, Enums\AssociateType::values());
        $this->assertCount(4, Enums\CancelType::values());
        $this->assertCount(6, Enums\DeductionType::values());
        $this->assertCount(2, Enums\DiscountType::values());
        $this->assertCount(23, Enums\DocumentType::values());
        $this->assertCount(2, Enums\EmittingSubject::values());
        $this->assertCount(22, Enums\FundType::values());
        $this->assertCount(23, Enums\PaymentMethod::values());
        $this->assertCount(3, Enums\PaymentTerm::values());
        $this->assertCount(19, Enums\TaxRegime::values());
        $this->assertCount(3, Enums\TransmissionFormat::values());
        $this->assertCount(3, Enums\VatEligibility::values());
        $this->assertCount(2, Enums\WoundUpType::values());
    }
}
