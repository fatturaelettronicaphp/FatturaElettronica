<?php

/**
 * Created by PhpStorm.
 * User: Vetar
 * Date: 21/11/2021
 * Time: 16:56
 */

namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DeductionInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Deduction;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DeductionType;
use PHPUnit\Framework\TestCase;

class DeductionTest extends TestCase
{
    /** @test */
    public function can_insert_multiple_deduction_nodes(): void
    {
        $file = __DIR__ . '/fixtures/IT01234567890_11001.xml';
        $eDocument = DigitalDocument::parseFrom($file);

        $rows = $eDocument->getDocumentInstances();

        /** @var DigitalDocumentInstanceInterface $firstRow */
        $firstRow = array_shift($rows);

        $deduction_one = (new Deduction())
            ->setType('RT01')
            ->setAmount(10.00)
            ->setPercentage(20.00)
            ->setDescription('A');

        $deduction_two = (new Deduction())
            ->setType('RT02')
            ->setAmount(5.00)
            ->setPercentage(15.00)
            ->setDescription('Q');

        $firstRow
            ->addDeduction($deduction_one)
            ->addDeduction($deduction_two);

        $rows = $eDocument->getDocumentInstances();

        /** @var DigitalDocumentInstanceInterface $firstRow */
        $firstRow = array_shift($rows);

        /** @var DeductionInterface $deduction_one */
        /** @var DeductionInterface $deduction_two */
        $deduction_one = $firstRow->getDeductions()[0];
        $deduction_two = $firstRow->getDeductions()[1];

        $this->assertEquals('RT01', $deduction_one->getType()->value);
        $this->assertEquals(10.00, $deduction_one->getAmount());
        $this->assertEquals(20.00, $deduction_one->getPercentage());
        $this->assertEquals('A', $deduction_one->getDescription());

        $this->assertEquals('RT02', $deduction_two->getType()->value);
        $this->assertEquals(5.00, $deduction_two->getAmount());
        $this->assertEquals(15.00, $deduction_two->getPercentage());
        $this->assertEquals('Q', $deduction_two->getDescription());
    }

    /** @test */
    public function can_parse_a_document_with_multiple_deduction_nodes(): void
    {
        $file = __DIR__ . '/fixtures/IT01234567890_11001_multi_ritenute.xml';
        $eDocument = DigitalDocument::parseFrom($file);

        $rows = $eDocument->getDocumentInstances();

        /** @var DigitalDocumentInstanceInterface $firstRow */
        $firstRow = array_shift($rows);

        /** @var DeductionInterface $deduction_one */
        /** @var DeductionInterface $deduction_two */
        $deduction_one = $firstRow->getDeductions()[0];
        $deduction_two = $firstRow->getDeductions()[1];

        $this->assertEquals(DeductionType::RT01, $deduction_one->getType());
        $this->assertEquals(10.00, $deduction_one->getAmount());
        $this->assertEquals(20.00, $deduction_one->getPercentage());
        $this->assertEquals('A', $deduction_one->getDescription());

        $this->assertEquals(DeductionType::RT02, $deduction_two->getType());
        $this->assertEquals(5.00, $deduction_two->getAmount());
        $this->assertEquals(15.00, $deduction_two->getPercentage());
        $this->assertEquals('Q', $deduction_two->getDescription());
    }
}
