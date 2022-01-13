<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Address;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\BillablePersonInterface;
use FatturaElettronicaPhp\FatturaElettronica\Customer;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocumentInstance;
use FatturaElettronicaPhp\FatturaElettronica\Enums\CancelType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DeductionType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DocumentType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\EmittingSubject;
use FatturaElettronicaPhp\FatturaElettronica\Enums\PaymentMethod;
use FatturaElettronicaPhp\FatturaElettronica\Enums\PaymentReason;
use FatturaElettronicaPhp\FatturaElettronica\Enums\PaymentTerm;
use FatturaElettronicaPhp\FatturaElettronica\Enums\TaxRegime;
use FatturaElettronicaPhp\FatturaElettronica\Enums\TransmissionFormat;
use FatturaElettronicaPhp\FatturaElettronica\Enums\VatEligibility;
use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;
use FatturaElettronicaPhp\FatturaElettronica\Intermediary;
use FatturaElettronicaPhp\FatturaElettronica\Line;
use FatturaElettronicaPhp\FatturaElettronica\OtherData;
use FatturaElettronicaPhp\FatturaElettronica\PaymentDetails;
use FatturaElettronicaPhp\FatturaElettronica\PaymentInfo;
use FatturaElettronicaPhp\FatturaElettronica\Representative;
use FatturaElettronicaPhp\FatturaElettronica\Shipment;
use FatturaElettronicaPhp\FatturaElettronica\ShippingLabel;
use FatturaElettronicaPhp\FatturaElettronica\Supplier;
use FatturaElettronicaPhp\FatturaElettronica\Total;
use PHPUnit\Framework\TestCase;

class CustomWriteDigitalDocumentTest extends TestCase
{
    /** @test */
    public function writes_shipment_address()
    {
        $supplier = $this
            ->generatePerson(new Supplier())
            ->setRegister("123")
            ->setRegisterDate(new DateTime());

        $customer = $this
            ->generatePerson(new Customer())
            ->setRepresentative($this->generatePerson(new Representative()));

        $document = new DigitalDocument();
        $document
            ->setSendingId("123")
            ->setCountryCode("IT")
            ->setSenderVatId("00000000000")
            ->setCustomer($customer)
            ->setSupplier($supplier)
            ->setRepresentative($this->generatePerson(new Representative()))
            ->setCustomerPec("test@pec.it")
            ->setCustomerSdiCode("123456")
            ->setEmittingSubject(EmittingSubject::CC())
            ->setEmittingSystem("TEST")
            ->setVersion(TransmissionFormat::FPA12())
            ->setIntermediary($this->generatePerson(new Intermediary()))
            ->setSenderEmail("test@example.com")
            ->setSenderPhone("123456788")
            ->setSenderVatId("00000000000")
            ->setTransmissionFormat(TransmissionFormat::FPA12());

        $shipment = (new Shipment())
            ->setNetWeight(7)
            ->setWeight(8)
            ->setDescription("TEST")
            ->setMethod("TEST")
            ->setShipmentDate(new DateTime())
            ->setDeliveryDate(new DateTime())
            ->setShipmentDescription("TEST")
            ->setNumberOfPackages(1)
            ->setPickupDate(new DateTime())
            ->setReturnAddress($this->generateAddress())
            ->setReturnType("TES");

        $line = (new Line())
            ->setNumber(1)
            ->setAdministrativeContact("TEST")
            ->setDeduction(false)
            ->setDescription("TEST")
            ->setEndDate(new DateTime())
            ->setQuantity(1)
            ->setStartDate(new DateTime())
            ->setTaxPercentage(0)
            ->setTipoCessazionePrestazione(CancelType::AB())
            ->setTotal(100)
            ->setUnit('P')
            ->setUnitPrice(100)
            ->setVatNature(VatNature::N1())
            ->addOtherData(
                (new OtherData())
                    ->setDate(new DateTime())
                    ->setNumber(42)
                    ->setText("TEST")
                    ->setType("TEST")
            );

        /** @var DigitalDocumentInstance $instance */
        $instance = (new DigitalDocumentInstance())
            ->setDocumentNumber(1)
            ->setDocumentDate(new DateTime())
            ->setArt73(false)
            ->setDocumentType(DocumentType::TD01())
            ->setCurrency("EUR")
            ->setDocumentTotal(100)
            ->setDeductionAmount(0)
            ->setDeductionDescription(PaymentReason::A())
            ->setDeductionPercentage(0)
            ->setDeductionType(DeductionType::RT01())
            ->setMainInvoiceDate(new DateTime())
            ->setMainInvoiceNumber(1)
            ->setRounding(0)
            ->setVehicleRegistrationDate(new DateTime())
            ->setVehicleTotalKm(100)
            ->setShipment($shipment)
            ->addLine($line)
            ->addShippingLabel(
                (new ShippingLabel())
                    ->setDocumentDate(new DateTime())
                    ->setDocumentNumber(13)
            )
            ->addPaymentInformations(
                (new PaymentInfo())
                    ->setTerms(
                        PaymentTerm::TP01()
                    )
                    ->addDetails(
                        (new PaymentDetails())
                            ->setMethod(
                                PaymentMethod::MP01()
                            )
                            ->setLatePaymentFee(12)
                            ->setEarlyPaymentDiscount(10)
                    )
            );

        $instance->addTotal(
            (new Total())
                ->setRounding(0)
                ->setVatNature(VatNature::N1())
                ->setTotal($instance->calculateDocumentTotal())
                ->setTaxPercentage(0)
                ->setOtherExpenses(0)
                ->setReference("123")
                ->setTaxAmount(0)
                ->setTaxType(VatEligibility::D())
        );

        $document->addDigitalDocumentInstance($instance);

        $this->assertTrue($document->validate()->isValid(), json_encode($document->validate()->errors()));

        $xml = $document->serialize()->asXML();

        $this->assertStringContainsString("<NumeroCivico>123</NumeroCivico>", $xml);
        $this->assertStringContainsString("<Provincia>VI</Provincia>", $xml);
        $this->assertStringContainsString("<RiferimentoNumero>42.00</RiferimentoNumero>", $xml);
        $this->assertStringContainsString("<ScontoPagamentoAnticipato>10.00</ScontoPagamentoAnticipato>", $xml);
        $this->assertStringContainsString("<PenalitaPagamentiRitardati>12.00</PenalitaPagamentiRitardati>", $xml);
        $this->assertStringContainsString("<PesoNetto>7.00</PesoNetto>", $xml);
        $this->assertStringContainsString("<PesoLordo>8.00</PesoLordo>", $xml);
    }

    private function generatePerson(BillablePersonInterface $person)
    {
        return $person
            ->setVatNumber("12345678910")
            ->setAddress($this->generateAddress())
            ->setCountryCode("IT")
            ->setFiscalCode("12345678910")
            ->setEori(123)
            ->setForeignFixedAddress($this->generateAddress())
            ->setName("TEST")
            ->setOrganization("TEST");
    }

    private function generateAddress(): \FatturaElettronicaPhp\FatturaElettronica\Contracts\AddressInterface
    {
        return (new Address())
            ->setStreet("Via Test")
            ->setZip("36100")
            ->setCity("Vicenza")
            ->setStreetNumber("123")
            ->setState("VI")
            ->setCountryCode("IT");
    }
}
