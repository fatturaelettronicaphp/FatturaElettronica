<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Supplier;
use PHPUnit\Framework\TestCase;

class SupplierRegisterDateTest extends TestCase
{
    /** @test */
    public function a_register_date_string_is_kept()
    {
        $supplier = new Supplier();
        $supplier->setRegisterDate('2018-05-01');

        $this->assertSame('2018-05-01', $supplier->getRegisterDate()->format('Y-m-d'));
    }

    /** @test */
    public function a_register_date_object_is_kept()
    {
        $supplier = new Supplier();
        $supplier->setRegisterDate(new DateTime('2018-05-01'));

        $this->assertSame('2018-05-01', $supplier->getRegisterDate()->format('Y-m-d'));
    }
}
