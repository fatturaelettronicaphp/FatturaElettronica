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

    /** @test */
    public function a_bad_register_date_becomes_today()
    {
        $supplier = new Supplier();
        $supplier->setRegisterDate('not-a-date');

        $this->assertSame((new DateTime())->format('Y-m-d'), $supplier->getRegisterDate()->format('Y-m-d'));
    }

    /** @test */
    public function a_bad_formatted_register_date_becomes_today()
    {
        $supplier = new Supplier();
        $supplier->setRegisterDate('not-a-date', 'Y-m-d');

        $this->assertSame((new DateTime())->format('Y-m-d'), $supplier->getRegisterDate()->format('Y-m-d'));
    }
}
