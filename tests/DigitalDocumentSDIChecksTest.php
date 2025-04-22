<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Address;
use FatturaElettronicaPhp\FatturaElettronica\Customer;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocumentInstance;
use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;
use FatturaElettronicaPhp\FatturaElettronica\Line;
use FatturaElettronicaPhp\FatturaElettronica\Supplier;
use FatturaElettronicaPhp\FatturaElettronica\Total;
use PHPUnit\Framework\TestCase;

class DigitalDocumentSDIChecksTest extends TestCase
{
    /**
     * @return void
     * @test
     */
    public function validatesMissingVatIdTaxId(): void
    {
        $eDocument = new DigitalDocument();
        $eDocument->setTransmissionFormat('FPR12');
        $eDocument->setCountryCode('IT');
        $eDocument->setSenderVatId('012345678910');
        $eDocument->setSendingId('123');

        $supplier = new Supplier();
        $supplier
            ->setCountryCode("IT")
            ->setVatNumber('123123123')
            ->setAddress((new Address())
                ->setCountryCode('IT')
                ->setCity("Milano")
                ->setZip("20125")
                ->setStreet("Piazza Duca d'Aosta")
                ->setStreetNumber("1"))
            ->setName("Nome")
            ->setSurname("Cognome");
        $eDocument->setSupplier($supplier);

        $customer = new Customer();
        $customer->setCountryCode("IT")
            ->setName("Nome")
            ->setSurname("Cognome");

        $customer->setAddress((new Address())
            ->setCountryCode('IT')
            ->setCity("Milano")
            ->setZip("20125")
            ->setStreet("Piazza Duca d'Aosta")
            ->setStreetNumber("1"));

        $eDocument->setCustomer($customer);

        $instance = new DigitalDocumentInstance();
        $instance->setDocumentType('TD01');
        $instance->setCurrency('EUR');
        $instance->setDocumentDate((new DateTime())->format('Y-m-d'));
        $instance->setDocumentNumber("1");

        $line = new Line();
        $line->setNumber(1);
        $line->setDescription("Servizio xyz");
        $line->setQuantity(1);
        $line->setUnitPrice(100);
        $line->setTaxPercentage(0);
        $line->setTotal(100);
        //$line->setVatNature(VatNature::N2_2());
        $instance->addLine($line);

        $total = new Total();
        $total->setTaxPercentage(0);
        $total->setVatNature(VatNature::N2_2());
        $total->setTotal(100);
        $total->setTaxAmount(0);
        $instance->addTotal($total);

        $eDocument->addDigitalDocumentInstance($instance);

        $this->assertFalse($eDocument->isValid());
        $errors = $eDocument->validate()->errors();

        $this->assertArrayHasKey("0.0.Natura", $errors);
        $this->assertStringContainsString("Errore 00400", array_shift($errors));
    }
}
