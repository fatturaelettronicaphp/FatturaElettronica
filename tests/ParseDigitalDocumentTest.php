<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use DateTime;
use Exception;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\AttachmentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\LineInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\PaymentDetailsInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\PaymentInfoInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\TotalInterface;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\Enums\TransmissionFormat;
use PHPUnit\Framework\TestCase;

class ParseDigitalDocumentTest extends TestCase
{
    /** @test */
    public function can_read_p7m_invoice()
    {
        $file = __DIR__ . '/fixtures/IT00484960588_ERKHK.xml.p7m';

        $eDocument = DigitalDocument::parseFrom($file);
        $this->assertTrue($eDocument instanceof DigitalDocumentInterface);
        $this->assertTrue($eDocument->getTransmissionFormat()->equals(TransmissionFormat::FPR12()));

        $this->assertEquals('03579410246', $eDocument->getCustomer()->getVatNumber());
        $this->assertEquals('WEBLE S.R.L.', $eDocument->getCustomer()->getOrganization());

        $this->assertEquals('00484960588', $eDocument->getSupplier()->getFiscalCode());
        $this->assertEquals('00905811006', $eDocument->getSupplier()->getVatNumber());
        $this->assertEquals('Eni SpADivisione Refining & Marketing', $eDocument->getSupplier()->getOrganization());

        $this->assertTrue($eDocument->isValid(), 'Is not Valid: ' . json_encode($eDocument->validate()->errors()));
    }

    /** @test */
    public function can_read_attachments()
    {
        $file = __DIR__ . '/fixtures/IT00484960588_ERKHK.xml.p7m';

        $eDocument = DigitalDocument::parseFrom($file);

        $this->assertTrue($eDocument instanceof DigitalDocumentInterface);

        $bodies = $eDocument->getDocumentInstances();
        /** @var DigitalDocumentInstanceInterface $body */
        $body = array_shift($bodies);

        $attachments = $body->getAttachments();
        /** @var AttachmentInterface $attachment */
        $attachment = array_shift($attachments);

        $this->assertEquals("000267590730122583.zip", $attachment->getName());
        $this->assertEquals("Allegato a documento elettronico", $attachment->getDescription());
        $this->assertNotEmpty($attachment->getAttachment());
        $this->assertNotEmpty($attachment->getFileData());

        $filePath = $attachment->writeFileToFolder();
        $this->assertTrue(file_exists($filePath));
        unlink($filePath);
    }

    /** @test */
    public function can_encode_decode_attachment()
    {
        $file = __DIR__ . '/fixtures/IT00484960588_ERKHK.xml.p7m';

        $eDocument = DigitalDocument::parseFrom($file);

        $bodies = $eDocument->getDocumentInstances();
        /** @var DigitalDocumentInstanceInterface $body */
        $body = array_shift($bodies);

        $attachments = $body->getAttachments();
        /** @var AttachmentInterface $attachment */
        $attachment = array_shift($attachments);

        $this->assertEquals(base64_decode($attachment->getAttachment()), $attachment->getFileData());
        $this->assertEquals($attachment->getAttachment(), base64_encode($attachment->getFileData()));
    }

    /** @test */
    public function can_read_xml_invoice_file()
    {
        $file = __DIR__ . '/fixtures/IT01234567890_FPR02.xml';

        $eDocument = DigitalDocument::parseFrom($file);

        $this->validateDocument($eDocument);
    }

    /** @test */
    public function can_read_xml_invoice()
    {
        $file      = __DIR__ . '/fixtures/IT01234567890_FPR02.xml';
        $xml       = simplexml_load_file($file);
        $eDocument = DigitalDocument::parseFrom($xml);

        $this->validateDocument($eDocument);
    }

    /** @test */
    public function can_read_complex_xml_invoice()
    {
        $file      = __DIR__ . '/fixtures/IT01234567899_000sq.xml';
        $xml       = simplexml_load_file($file);
        $eDocument = DigitalDocument::parseFrom($xml);

        $this->validateComplexDocument($eDocument);
    }

    /**
     * @param DigitalDocumentInterface $eDocument
     *
     * @return array
     * @throws Exception
     */
    protected function validateDocument(DigitalDocumentInterface $eDocument)
    {
        $this->assertTrue($eDocument instanceof DigitalDocumentInterface);

        // Trasmissione
        $this->assertTrue($eDocument->getTransmissionFormat()->equals(TransmissionFormat::FPR12()));
        $this->assertEquals('IT', $eDocument->getCountryCode());
        $this->assertEquals('betagamma@pec.it', $eDocument->getCustomerPec());
        $this->assertEquals('0000000', $eDocument->getCustomerSdiCode());

        // Fornitore
        $this->assertEquals('IT', $eDocument->getSupplier()->getCountryCode());
        $this->assertEquals(null, $eDocument->getSupplier()->getFiscalCode());
        $this->assertEquals('RF01', (string)$eDocument->getSupplier()->getTaxRegime());
        $this->assertEquals('01234567890', $eDocument->getSupplier()->getVatNumber());
        $this->assertEquals('SOCIETA\' ALPHA SRL', $eDocument->getSupplier()->getOrganization());

        $this->assertEquals('VIALE ROMA 543', $eDocument->getSupplier()->getAddress()->getStreet());
        $this->assertEquals('07100', $eDocument->getSupplier()->getAddress()->getZip());
        $this->assertEquals('SASSARI', $eDocument->getSupplier()->getAddress()->getCity());
        $this->assertEquals('SS', $eDocument->getSupplier()->getAddress()->getState());
        $this->assertEquals('IT', $eDocument->getSupplier()->getAddress()->getCountryCode());

        // Cliente
        $this->assertEquals('', $eDocument->getCustomer()->getVatNumber());
        $this->assertEquals('09876543210', $eDocument->getCustomer()->getFiscalCode());
        $this->assertEquals('BETA GAMMA', $eDocument->getCustomer()->getOrganization());

        $this->assertEquals('VIA TORINO 38-B', $eDocument->getCustomer()->getAddress()->getStreet());
        $this->assertEquals('00145', $eDocument->getCustomer()->getAddress()->getZip());
        $this->assertEquals('ROMA', $eDocument->getCustomer()->getAddress()->getCity());
        $this->assertEquals('RM', $eDocument->getCustomer()->getAddress()->getState());
        $this->assertEquals('IT', $eDocument->getCustomer()->getAddress()->getCountryCode());

        // Corpo
        $rows = $eDocument->getDocumentInstances();

        /** @var DigitalDocumentInstanceInterface $firstRow */
        $firstRow = array_shift($rows);

        $this->assertEquals('TD01', (string)$firstRow->getDocumentType());
        $this->assertEquals('EUR', $firstRow->getCurrency());
        $this->assertEquals(new DateTime('2014-12-18'), $firstRow->getDocumentDate());
        $this->assertEquals('123', $firstRow->getDocumentNumber());
        $this->assertEquals(
            'LA FATTURA FA RIFERIMENTO AD UNA OPERAZIONE AAAA BBBBBBBBBBBBBBBBBB CCC DDDDDDDDDDDDDDD E FFFFFFFFFFFFFFFFFFFF GGGGGGGGGG HHHHHHH II LLLLLLLLLLLLLLLLL MMM NNNNN OO PPPPPPPPPPP QQQQ RRRR SSSSSSSSSSSSSS',
            $firstRow->getDescriptions()[0]
        );
        $this->assertEquals(
            'SEGUE DESCRIZIONE CAUSALE NEL CASO IN CUI NON SIANO STATI SUFFICIENTI 200 CARATTERI AAAAAAAAAAA BBBBBBBBBBBBBBBBB',
            $firstRow->getDescriptions()[1]
        );

        // Righe
        $products = $firstRow->getLines();
        /** @var LineInterface $firstProduct */
        $firstProduct = array_shift($products);

        $this->assertEquals(1, $firstProduct->getNumber());
        $this->assertEquals(
            "LA DESCRIZIONE DELLA FORNITURA PUO' SUPERARE I CENTO CARATTERI CHE RAPPRESENTAVANO IL PRECEDENTE LIMITE DIMENSIONALE. TALE LIMITE NELLA NUOVA VERSIONE E' STATO PORTATO A MILLE CARATTERI",
            $firstProduct->getDescription()
        );
        $this->assertEquals(5, $firstProduct->getQuantity());
        $this->assertEquals(1, $firstProduct->getUnitPrice());
        $this->assertEquals(5, $firstProduct->getTotal());
        $this->assertEquals(22, $firstProduct->getTaxPercentage());

        // Payment Info
        $paymentInfos = $firstRow->getPaymentInformations();
        /** @var PaymentInfoInterface $info */
        $info = array_shift($paymentInfos);

        $this->assertEquals('TP01', $info->getTerms());

        $details = $info->getDetails();
        /** @var PaymentDetailsInterface $detail */
        $detail = array_shift($details);
        $this->assertEquals('MP01', $detail->getMethod());
        $this->assertEquals(new DateTime('2015-01-30'), $detail->getDueDate());
        $this->assertEquals(30.50, $detail->getAmount());

        $this->assertTrue($eDocument->isValid(), 'Is Invalid: ' . json_encode($eDocument->validate()->errors()));
    }

    /**
     * @param DigitalDocumentInterface $eDocument
     *
     * @return array
     * @throws Exception
     */
    protected function validateComplexDocument(DigitalDocumentInterface $eDocument)
    {
        $this->assertTrue($eDocument instanceof DigitalDocumentInterface);

        // Trasmissione
        $this->assertTrue($eDocument->getTransmissionFormat()->equals(TransmissionFormat::FPR12()));
        $this->assertEquals('IT', $eDocument->getCountryCode());
        $this->assertNull($eDocument->getCustomerPec());
        $this->assertEquals('C3UCNRB', $eDocument->getCustomerSdiCode());

        // Fornitore
        $this->assertEquals('IT', $eDocument->getSupplier()->getCountryCode());
        $this->assertEquals('01234567899', $eDocument->getSupplier()->getFiscalCode());
        $this->assertEquals('RF01', (string)$eDocument->getSupplier()->getTaxRegime());
        $this->assertEquals('01234567899', $eDocument->getSupplier()->getVatNumber());
        $this->assertEquals('ACME SPA', $eDocument->getSupplier()->getOrganization());
        $this->assertEquals('RF01', (string) $eDocument->getSupplier()->getTaxRegime());

        $this->assertEquals('VIA ALFREDO BIANCHI', $eDocument->getSupplier()->getAddress()->getStreet());
        $this->assertEquals('111', $eDocument->getSupplier()->getAddress()->getStreetNumber());
        $this->assertEquals('30010', $eDocument->getSupplier()->getAddress()->getZip());
        $this->assertEquals('MILANO', $eDocument->getSupplier()->getAddress()->getCity());
        $this->assertEquals('VI', $eDocument->getSupplier()->getAddress()->getState());
        $this->assertEquals('IT', $eDocument->getSupplier()->getAddress()->getCountryCode());

        // Cliente
        $this->assertEquals('', $eDocument->getCustomer()->getVatNumber());
        $this->assertEquals('01234567894', $eDocument->getCustomer()->getFiscalCode());
        $this->assertEquals('Azienda della Fattura Srl', $eDocument->getCustomer()->getOrganization());

        $this->assertEquals('Via Rossi', $eDocument->getCustomer()->getAddress()->getStreet());
        $this->assertEquals('222', $eDocument->getCustomer()->getAddress()->getStreetNumber());
        $this->assertEquals('31100', $eDocument->getCustomer()->getAddress()->getZip());
        $this->assertEquals('MILANO', $eDocument->getCustomer()->getAddress()->getCity());
        $this->assertEquals('VI', $eDocument->getCustomer()->getAddress()->getState());
        $this->assertEquals('IT', $eDocument->getCustomer()->getAddress()->getCountryCode());

        // Corpo
        $rows = $eDocument->getDocumentInstances();

        /** @var DigitalDocumentInstanceInterface $firstRow */
        $firstRow = array_shift($rows);

        $this->assertEquals('TD01', (string)$firstRow->getDocumentType());
        $this->assertEquals('EUR', $firstRow->getCurrency());
        $this->assertEquals(new DateTime('2019-03-19'), $firstRow->getDocumentDate());
        $this->assertEquals('1', $firstRow->getDocumentNumber());
        $this->assertEquals(
            'Descrizione della causale del documento AAAABBBBBB 1324325y82973482 hbtg2vy14t5fy',
            $firstRow->getDescriptions()[0]
        );

        // Ritenuta
        $this->assertEquals('RT02', (string)$firstRow->getDeductionType());
        $this->assertEquals(4.41, $firstRow->getDeductionAmount());
        $this->assertEquals(11.50, $firstRow->getDeductionPercentage());
        $this->assertEquals('U', $firstRow->getDeductionDescription());

        // Funds
        $funds = $firstRow->getFunds();

        $firstFund = array_shift($funds);
        $this->assertEquals('TC03', (string)$firstFund->getType());
        $this->assertEquals(4, $firstFund->getPercentage());
        $this->assertEquals(36.88, $firstFund->getAmount());
        $this->assertEquals(922.00, $firstFund->getSubtotal());
        $this->assertEquals(22, $firstFund->getTaxPercentage());

        $firstFund = array_shift($funds);
        $this->assertEquals('TC22', (string)$firstFund->getType());
        $this->assertEquals(4, $firstFund->getPercentage());
        $this->assertEquals(38.36, $firstFund->getAmount());
        $this->assertEquals(958.88, $firstFund->getSubtotal());
        $this->assertEquals(22, $firstFund->getTaxPercentage());
        $this->assertTrue($firstFund->hasDeduction());

        $this->assertEquals(2486.02, $firstRow->getDocumentTotal());
        $this->assertEquals('Descrizione della causale del documento AAAABBBBBB 1324325y82973482 hbtg2vy14t5fy', implode(" ", $firstRow->getDescriptions()));

        // Righe
        $products = $firstRow->getLines();
        /** @var LineInterface $firstProduct */
        $firstProduct = array_shift($products);

        $this->assertEquals(1, $firstProduct->getNumber());
        $this->assertEquals(
            "PRODOTTO A",
            $firstProduct->getDescription()
        );
        $this->assertEquals(1, $firstProduct->getQuantity());
        $this->assertEquals(652, $firstProduct->getUnitPrice());
        $this->assertEquals(652, $firstProduct->getTotal());
        $this->assertEquals(22, $firstProduct->getTaxPercentage());
        $this->assertEquals(new DateTime('2019-03-19'), $firstProduct->getStartDate());
        $this->assertEquals(new DateTime('2020-03-18'), $firstProduct->getEndDate());

        $datas     = $firstProduct->getOtherData();
        $otherData = array_shift($datas);
        $this->assertEquals('CASSA-PREV', (string) $otherData->getType());
        $this->assertEquals('ENASARCO TC07',  $otherData->getText());
        $this->assertEquals(53.79,  $otherData->getNumber());

        /** @var LineInterface $firstProduct */
        $firstProduct = array_shift($products);

        $this->assertEquals(2, $firstProduct->getNumber());
        $this->assertEquals(
            "Prodotto B",
            $firstProduct->getDescription()
        );
        $this->assertEquals(1, $firstProduct->getQuantity());
        $this->assertEquals(452, $firstProduct->getUnitPrice());
        $this->assertEquals(452, $firstProduct->getTotal());
        $this->assertEquals(0, $firstProduct->getTaxPercentage());
        $this->assertEquals('N2', (string) $firstProduct->getVatNature());

        /** @var LineInterface $firstProduct */
        $firstProduct = array_shift($products);

        $this->assertEquals(3, $firstProduct->getNumber());
        $this->assertEquals(
            "Prodotto con Sconto 10%",
            $firstProduct->getDescription()
        );
        $this->assertEquals(1, $firstProduct->getQuantity());
        $this->assertEquals(300, $firstProduct->getUnitPrice());
        $this->assertEquals(270, $firstProduct->getTotal());
        $this->assertEquals(22, $firstProduct->getTaxPercentage());

        $discounts = $firstProduct->getDiscounts();
        /** @var DiscountInterface $discount */
        $discount = array_shift($discounts);
        $this->assertEquals('SC', (string) $discount->getType());
        $this->assertEquals(10, $discount->getPercentage());

        /** @var LineInterface $firstProduct */
        $firstProduct = array_shift($products);

        $this->assertEquals(4, $firstProduct->getNumber());
        $this->assertEquals(
            "Prodotto split payment 1",
            $firstProduct->getDescription()
        );
        $this->assertEquals(1, $firstProduct->getQuantity());
        $this->assertEquals(20, $firstProduct->getUnitPrice());
        $this->assertEquals(20, $firstProduct->getTotal());
        $this->assertEquals(22, $firstProduct->getTaxPercentage());

        /** @var LineInterface $firstProduct */
        $firstProduct = array_shift($products);

        $this->assertEquals(5, $firstProduct->getNumber());
        $this->assertEquals(
            "Prodotto split payment 2",
            $firstProduct->getDescription()
        );
        $this->assertEquals(1, $firstProduct->getQuantity());
        $this->assertEquals(650, $firstProduct->getUnitPrice());
        $this->assertEquals(650, $firstProduct->getTotal());
        $this->assertEquals(22, $firstProduct->getTaxPercentage());

        $totals = $firstRow->getTotals();
        /** @var TotalInterface $total */
        $total = array_shift($totals);
        $this->assertEquals(22, $total->getTaxPercentage());
        $this->assertEquals(0, $total->getOtherExpenses());
        $this->assertEquals(705.20, $total->getTotal());
        $this->assertEquals(149.41, $total->getTaxAmount());
        $this->assertEquals('I', (string) $total->getTaxType());

        /** @var TotalInterface $total */
        $total = array_shift($totals);
        $this->assertEquals(0, $total->getTaxPercentage());
        $this->assertEquals(0, $total->getOtherExpenses());
        $this->assertEquals(452, $total->getTotal());
        $this->assertEquals(0, $total->getTaxAmount());
        $this->assertEquals('I', (string) $total->getTaxType());
        $this->assertEquals('N2', (string) $total->getVatNature());
        $this->assertEquals('ESCLUSI ART.3 C.4 DPR 633/72', $total->getReference());

        /** @var TotalInterface $total */
        $total = array_shift($totals);
        $this->assertEquals(22, $total->getTaxPercentage());
        $this->assertEquals(0, $total->getOtherExpenses());
        $this->assertEquals(292.03, $total->getTotal());
        $this->assertEquals(61.87, $total->getTaxAmount());
        $this->assertEquals('D', (string) $total->getTaxType());

        /** @var TotalInterface $total */
        $total = array_shift($totals);
        $this->assertEquals(22, $total->getTaxPercentage());
        $this->assertEquals(0, $total->getOtherExpenses());
        $this->assertEquals(670.00, $total->getTotal());
        $this->assertEquals(147.40, $total->getTaxAmount());
        $this->assertEquals('S', (string) $total->getTaxType());

        // Payment Info
        $paymentInfos = $firstRow->getPaymentInformations();
        $this->assertCount(0, $paymentInfos);

        $this->assertTrue($eDocument->isValid(), json_encode($eDocument->validate()->errors()));
    }
}
