<?php


namespace Weble\FatturaElettronica\Tests;

use PHPUnit\Framework\TestCase;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Contracts\PaymentDetailsInterface;
use Weble\FatturaElettronica\Contracts\PaymentInfoInterface;
use Weble\FatturaElettronica\Enums\TransmissionFormat;
use Weble\FatturaElettronica\Parser\DigitalDocumentParser;

class ParseDigitalDocumentTest extends TestCase
{
    /** @test */
    public function can_read_p7m_invoice ()
    {
        $file = dirname(__FILE__) . '/fixtures/IT00484960588_ERKHK.xml.p7m';
        $documentParser = new DigitalDocumentParser($file);

        $eDocument = $documentParser->parse();

        $this->assertTrue($eDocument instanceof DigitalDocumentInterface);

        $this->assertTrue($eDocument->getTransmissionFormat()->equals(TransmissionFormat::FPR12()));

        $this->assertEquals('03579410246', $eDocument->getCustomer()->getVatNumber());
        $this->assertEquals('WEBLE S.R.L.', $eDocument->getCustomer()->getOrganization());

        $this->assertEquals('00484960588', $eDocument->getSupplier()->getFiscalCode());
        $this->assertEquals('00905811006', $eDocument->getSupplier()->getVatNumber());
        $this->assertEquals('Eni SpADivisione Refining & Marketing', $eDocument->getSupplier()->getOrganization());
    }

    /** @test */
    public function can_read_xml_invoice ()
    {
        $file = dirname(__FILE__) . '/fixtures/IT01234567890_FPR02.xml';
        $documentParser = new DigitalDocumentParser($file);

        $eDocument = $documentParser->parse();

        $this->assertTrue($eDocument instanceof DigitalDocumentInterface);

        // Trasmissione
        $this->assertTrue($eDocument->getTransmissionFormat()->equals(TransmissionFormat::FPR12()));
        $this->assertEquals('IT', $eDocument->getCountryCode());
        $this->assertEquals('betagamma@pec.it', $eDocument->getCustomerPec());
        $this->assertEquals('0000000', $eDocument->getCustomerSdiCode());

        // Fornitore
        $this->assertEquals('IT', $eDocument->getSupplier()->getCountryCode());
        $this->assertEquals(null, $eDocument->getSupplier()->getFiscalCode());
        $this->assertEquals('RF01', (string) $eDocument->getSupplier()->getTaxRegime());
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

        /** @var \Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface $firstRow */
        $firstRow = array_shift($rows);

        $this->assertEquals('TD01', (string) $firstRow->getDocumentType());
        $this->assertEquals('EUR',  $firstRow->getCurrency());
        $this->assertEquals(new \DateTime('2014-12-18'),  $firstRow->getDocumentDate());
        $this->assertEquals('123',  $firstRow->getDocumentNumber());
        $this->assertEquals('LA FATTURA FA RIFERIMENTO AD UNA OPERAZIONE AAAA BBBBBBBBBBBBBBBBBB CCC DDDDDDDDDDDDDDD E FFFFFFFFFFFFFFFFFFFF GGGGGGGGGG HHHHHHH II LLLLLLLLLLLLLLLLL MMM NNNNN OO PPPPPPPPPPP QQQQ RRRR SSSSSSSSSSSSSS',  $firstRow->getDescriptions()[0]);
        $this->assertEquals('SEGUE DESCRIZIONE CAUSALE NEL CASO IN CUI NON SIANO STATI SUFFICIENTI 200 CARATTERI AAAAAAAAAAA BBBBBBBBBBBBBBBBB',  $firstRow->getDescriptions()[1]);

        // Righe
        $products = $firstRow->getLines();
        /** @var \Weble\FatturaElettronica\Contracts\LineInterface $firstProduct */
        $firstProduct = array_shift($products);

        $this->assertEquals(1, $firstProduct->getNumber());
        $this->assertEquals("LA DESCRIZIONE DELLA FORNITURA PUO' SUPERARE I CENTO CARATTERI CHE RAPPRESENTAVANO IL PRECEDENTE LIMITE DIMENSIONALE. TALE LIMITE NELLA NUOVA VERSIONE E' STATO PORTATO A MILLE CARATTERI", $firstProduct->getDescription());
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
        $this->assertEquals(new \DateTime('2015-01-30'), $detail->getDueDate());
        $this->assertEquals(30.50, $detail->getAmount());
    }
}