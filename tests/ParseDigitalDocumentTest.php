<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use DateTime;
use Exception;
use FatturaElettronicaPhp\FatturaElettronica\Address;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\AttachmentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\LineInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\PaymentDetailsInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\PaymentInfoInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\TotalInterface;
use FatturaElettronicaPhp\FatturaElettronica\Customer;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocumentInstance;
use FatturaElettronicaPhp\FatturaElettronica\Enums\PaymentMethod;
use FatturaElettronicaPhp\FatturaElettronica\Enums\PaymentTerm;
use FatturaElettronicaPhp\FatturaElettronica\Enums\TransmissionFormat;
use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;
use FatturaElettronicaPhp\FatturaElettronica\Line;
use FatturaElettronicaPhp\FatturaElettronica\ShippingLabel;
use FatturaElettronicaPhp\FatturaElettronica\Supplier;
use FatturaElettronicaPhp\FatturaElettronica\Total;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class ParseDigitalDocumentTest extends TestCase
{
    /**
     * @test
     * @dataProvider listOfInvoices
     */
    public function can_read_p7m_invoices(string $filePath): void
    {
        $eDocument = DigitalDocument::parseFrom($filePath);
        $this->assertTrue($eDocument instanceof DigitalDocumentInterface);
        $this->assertFalse($eDocument->isSimplified());

        $this->assertTrue($eDocument->isValid(), json_encode($eDocument->validate()->errors()));
    }

    /**
     * @test
     */
    public function validates_dashed_emails_domains(): void
    {
        $eDocument = DigitalDocument::parseFrom(__DIR__ . '/fixtures/IT01234567890_11002.xml');
        $this->assertTrue($eDocument instanceof DigitalDocumentInterface);
        $this->assertTrue($eDocument->isValid());
    }

    /**
     * @test
     */
    public function reads_slashes_correctly(): void
    {
        $eDocument = DigitalDocument::parseFrom(__DIR__ . '/fixtures/IT01234567890_11001_slash.xml');
        $this->assertTrue($eDocument instanceof DigitalDocumentInterface);
        $this->assertTrue($eDocument->isValid());
        $this->assertInstanceOf(SimpleXMLElement::class, $eDocument->serialize());

        $this->assertEquals("20/20/21G", $eDocument->getDocumentInstances()[0]->getDocumentNumber());
    }

    /** @test */
    public function can_read_p7m_invoice(): void
    {
        $file = __DIR__ . '/fixtures/IT00484960588_ERKHK.xml.p7m';

        $eDocument = DigitalDocument::parseFrom($file);
        $this->assertTrue($eDocument instanceof DigitalDocumentInterface);
        $this->assertFalse($eDocument->isSimplified());
        $this->assertTrue($eDocument->getTransmissionFormat() === \FatturaElettronicaPhp\FatturaElettronica\Enums\TransmissionFormat::FPR12);

        $this->assertEquals('03579410246', $eDocument->getCustomer()->getVatNumber());
        $this->assertEquals('WEBLE S.R.L.', $eDocument->getCustomer()->getOrganization());

        $this->assertEquals('00484960588', $eDocument->getSupplier()->getFiscalCode());
        $this->assertEquals('00905811006', $eDocument->getSupplier()->getVatNumber());
        $this->assertEquals('Eni SpADivisione Refining & Marketing', $eDocument->getSupplier()->getOrganization());

        $this->assertTrue($eDocument->isValid(), 'Is not Valid: ' . json_encode($eDocument->validate()->errors()));
    }

    /** @test */
    public function can_read_attachments(): void
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
    public function can_encode_decode_attachment(): void
    {
        $file = __DIR__ . '/fixtures/IT00484960588_ERKHK.xml.p7m';

        $eDocument = DigitalDocument::parseFrom($file);

        $bodies = $eDocument->getDocumentInstances();
        /** @var DigitalDocumentInstanceInterface $body */
        $body = array_shift($bodies);

        $attachments = $body->getAttachments();
        /** @var AttachmentInterface $attachment */
        $attachment = array_shift($attachments);

        $this->assertEquals(base64_decode((string) $attachment->getAttachment()), $attachment->getFileData());
        $this->assertEquals($attachment->getAttachment(), base64_encode((string) $attachment->getFileData()));
    }

    /** @test */
    public function can_read_xml_invoice_file(): void
    {
        $file = __DIR__ . '/fixtures/IT01234567890_FPR02.xml';

        $eDocument = DigitalDocument::parseFrom($file);

        $this->validateDocument($eDocument);
    }

    /** @test */
    public function can_read_xml_invoice(): void
    {
        $file = __DIR__ . '/fixtures/IT01234567890_FPR02.xml';
        $xml = simplexml_load_file($file);
        $eDocument = DigitalDocument::parseFrom($xml);

        $this->validateDocument($eDocument);
    }

    /** @test */
    public function can_read_complex_xml_invoice(): void
    {
        $file = __DIR__ . '/fixtures/IT01234567899_000sq.xml';
        $xml = simplexml_load_file($file);
        $eDocument = DigitalDocument::parseFrom($xml);

        $this->validateComplexDocument($eDocument);
    }

    /** @test */
    public function reads_admin_contact(): void
    {
        $file = __DIR__ . '/fixtures/IT01234567890_11001.xml';
        $xml = simplexml_load_file($file);
        $eDocument = DigitalDocument::parseFrom($xml);

        /** @var DigitalDocumentInstanceInterface $document */
        $document = $eDocument->getDocumentInstances()[0];
        /** @var LineInterface $line */
        $line = $document->getLines()[0];

        $this->assertEquals('Pinco Pallo', $line->getAdministrativeContact());
    }

    /** @test */
    public function reads_ddt_line_numbers(): void
    {
        $file = __DIR__ . '/fixtures/IT01234567890_21101.xml';
        $xml = simplexml_load_file($file);
        $eDocument = DigitalDocument::parseFrom($xml);

        /** @var DigitalDocumentInstanceInterface $document */
        $document = $eDocument->getDocumentInstances()[0];

        /** @var ShippingLabel $shippingLabel */
        $shippingLabel = $document->getShippingLabels()[0];

        $this->assertCount(4, $shippingLabel->getLineNumberReferences());
        $this->assertEquals(["10", "11", "12", "13"], $shippingLabel->getLineNumberReferences());
    }

    /** @test */
    public function reads_all_payment_details(): void
    {
        $file = __DIR__ . '/fixtures/IT01234567890_11001_payment_details.xml';
        $xml = simplexml_load_file($file);
        $eDocument = DigitalDocument::parseFrom($xml);

        /** @var DigitalDocumentInstanceInterface $document */
        $document = $eDocument->getDocumentInstances()[0];
        $infos = $document->getPaymentInformations();

        $this->assertCount(1, $infos);

        /** @var PaymentInfoInterface $info */
        $info = $infos[0];
        $this->assertEquals(PaymentTerm::TP01, $info->getTerms());

        /** @var PaymentDetailsInterface $details */
        $details = $info->getDetails();
        $this->assertCount(1, $details);

        /** @var PaymentDetailsInterface $detail */
        $detail = $details[0];

        $this->assertEquals('Mario Rossi', $detail->getPayee());
        $this->assertEquals(PaymentMethod::MP01, $detail->getMethod());
        $this->assertEquals('2015-01-30', $detail->getDueDate()->format('Y-m-d'));
        $this->assertEquals(60, $detail->getDueDays());
        $this->assertEquals('2014-01-30', $detail->getDueDateFrom()->format('Y-m-d'));
        $this->assertEquals(6.1, $detail->getAmount());
        $this->assertEquals('12345', $detail->getPostalOfficeCode());
        $this->assertEquals('Rossi', $detail->getPayerSurname());
        $this->assertEquals('Mario', $detail->getPayerName());
        $this->assertEquals('RSSMRA77E01L840X', $detail->getPayerFiscalCode());
        $this->assertEquals('Sign.', $detail->getPayerTitle());
        $this->assertEquals('Nome Banca', $detail->getBankName());
        $this->assertEquals('IT00000000000000000000000', $detail->getIban());
        $this->assertEquals('ABC123', $detail->getAbi());
        $this->assertEquals('12345', $detail->getCab());
        $this->assertEquals('67891', $detail->getBic());
        $this->assertEquals(10, $detail->getEarlyPaymentDiscount());
        $this->assertEquals('2014-02-01', $detail->getEarlyPaymentDateLimit()->format('Y-m-d'));
        $this->assertEquals(11, $detail->getLatePaymentFee());
        $this->assertEquals('2016-02-01', $detail->getLatePaymentDateLimit()->format('Y-m-d'));
        $this->assertEquals('ABC', $detail->getPaymentCode());
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
        if ($eDocument->getEmittingSystem()) {
            $this->assertEquals("TEST", $eDocument->getEmittingSystem());
        }
        $this->assertTrue($eDocument->getTransmissionFormat() === TransmissionFormat::FPR12);
        $this->assertEquals('IT', $eDocument->getCountryCode());
        $this->assertEquals('betagamma@pec.it', $eDocument->getCustomerPec());
        $this->assertEquals('0000000', $eDocument->getCustomerSdiCode());

        // Fornitore
        $this->assertEquals('IT', $eDocument->getSupplier()->getCountryCode());
        $this->assertEquals(null, $eDocument->getSupplier()->getFiscalCode());
        $this->assertEquals('RF01', $eDocument->getSupplier()->getTaxRegime()->value);
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

        $this->assertEquals('TD01', $firstRow->getDocumentType()->value);
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

        $this->assertEquals('TP01', $info->getTerms()->value);

        $details = $info->getDetails();
        /** @var PaymentDetailsInterface $detail */
        $detail = array_shift($details);
        $this->assertEquals('MP01', $detail->getMethod()->value);
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
        $this->assertTrue($eDocument->getTransmissionFormat() === TransmissionFormat::FPR12);
        $this->assertEquals('IT', $eDocument->getCountryCode());
        $this->assertNull($eDocument->getCustomerPec());
        $this->assertEquals('C3UCNRB', $eDocument->getCustomerSdiCode());

        // Fornitore
        $this->assertEquals('IT', $eDocument->getSupplier()->getCountryCode());
        $this->assertEquals('01234567899', $eDocument->getSupplier()->getFiscalCode());
        $this->assertEquals('RF01', $eDocument->getSupplier()->getTaxRegime()->value);
        $this->assertEquals('01234567899', $eDocument->getSupplier()->getVatNumber());
        $this->assertEquals('ACME SPA', $eDocument->getSupplier()->getOrganization());
        $this->assertEquals('RF01', $eDocument->getSupplier()->getTaxRegime()->value);

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

        $this->assertEquals('TD01', $firstRow->getDocumentType()->value);
        $this->assertEquals('EUR', $firstRow->getCurrency());
        $this->assertEquals(new DateTime('2019-03-19'), $firstRow->getDocumentDate());
        $this->assertEquals('1', $firstRow->getDocumentNumber());
        $this->assertEquals(
            'Descrizione della causale del documento AAAABBBBBB 1324325y82973482 hbtg2vy14t5fy',
            $firstRow->getDescriptions()[0]
        );

        // Ritenuta
        $this->assertEquals('RT02', $firstRow->getDeductionType()->value);
        $this->assertEquals(4.41, $firstRow->getDeductionAmount());
        $this->assertEquals(11.50, $firstRow->getDeductionPercentage());
        $this->assertEquals('U', $firstRow->getDeductionDescription());

        // Funds
        $funds = $firstRow->getFunds();

        $firstFund = array_shift($funds);
        $this->assertEquals('TC03', $firstFund->getType()->value);
        $this->assertEquals(4, $firstFund->getPercentage());
        $this->assertEquals(36.88, $firstFund->getAmount());
        $this->assertEquals(922.00, $firstFund->getSubtotal());
        $this->assertEquals(22, $firstFund->getTaxPercentage());

        $firstFund = array_shift($funds);
        $this->assertEquals('TC22', $firstFund->getType()->value);
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

        $datas = $firstProduct->getOtherData();
        $otherData = array_shift($datas);
        $this->assertEquals('CASSA-PREV', (string)$otherData->getType());
        $this->assertEquals('ENASARCO TC07', $otherData->getText());
        $this->assertEquals(53.79, $otherData->getNumber());

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
        $this->assertEquals('N2', $firstProduct->getVatNature()->value);

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
        $this->assertEquals('SC', $discount->getType()->value);
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
        $this->assertEquals('I', $total->getTaxType()->value);

        /** @var TotalInterface $total */
        $total = array_shift($totals);
        $this->assertEquals(0, $total->getTaxPercentage());
        $this->assertEquals(0, $total->getOtherExpenses());
        $this->assertEquals(452, $total->getTotal());
        $this->assertEquals(0, $total->getTaxAmount());
        $this->assertEquals('I', $total->getTaxType()->value);
        $this->assertEquals('N2', $total->getVatNature()->value);
        $this->assertEquals('ESCLUSI ART.3 C.4 DPR 633/72', $total->getReference());

        /** @var TotalInterface $total */
        $total = array_shift($totals);
        $this->assertEquals(22, $total->getTaxPercentage());
        $this->assertEquals(0, $total->getOtherExpenses());
        $this->assertEquals(292.03, $total->getTotal());
        $this->assertEquals(61.87, $total->getTaxAmount());
        $this->assertEquals('D', $total->getTaxType()->value);

        /** @var TotalInterface $total */
        $total = array_shift($totals);
        $this->assertEquals(22, $total->getTaxPercentage());
        $this->assertEquals(0, $total->getOtherExpenses());
        $this->assertEquals(670.00, $total->getTotal());
        $this->assertEquals(147.40, $total->getTaxAmount());
        $this->assertEquals('S', $total->getTaxType()->value);

        // Payment Info
        $paymentInfos = $firstRow->getPaymentInformations();
        $this->assertCount(0, $paymentInfos);

        $this->assertTrue($eDocument->isValid(), json_encode($eDocument->validate()->errors()));
    }

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
            ->setVatNumber('12345678910')
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
        $line->setVatNature(VatNature::N2_2);
        $instance->addLine($line);

        $total = new Total();
        $total->setTaxPercentage(0);
        $total->setVatNature(VatNature::N2_2);
        $total->setTotal(100);
        $total->setTaxAmount(0);
        $instance->addTotal($total);

        $eDocument->addDigitalDocumentInstance($instance);

        $this->assertTrue($eDocument->isValid(), json_encode($eDocument->validate()->errors()));
    }

    public function listOfInvoices(): array
    {
        // List of files that cannot be shared on GIT due to privacy reasons
        // But that can be placed into fixtures/private and run against this suite
        $privateDir = __DIR__ . '/fixtures/private';

        $privateFileTests = [];
        if (is_dir($privateDir)) {
            $files = scandir($privateDir);
            $privateFiles = array_filter($files, fn($file) => in_array(pathinfo((string) $file, PATHINFO_EXTENSION), ['xml', 'p7m']));

            foreach ($privateFiles as $privateFile) {
                $privateFileTests[basename($privateFile)] = [$privateDir . '/' . $privateFile];
            }
        }

        return array_merge([
            'IT01234567890_21101.xml' => [__DIR__ . '/fixtures/IT01234567890_21101.xml'],
            'IT01234567890_11001.xml' => [__DIR__ . '/fixtures/IT01234567890_11001.xml'],
            'IT01234567890_11001_spazi.xml' => [__DIR__ . '/fixtures/IT01234567890_11001_spazi.xml'],
            'IT01234567890_11001_slash.xml' => [__DIR__ . '/fixtures/IT01234567890_11001_slash.xml'],
            'IT01234567890_11001_entity.xml' => [__DIR__ . '/fixtures/IT01234567890_11001_entity.xml'],
            'IT01234567890_11001_reso.xml' => [__DIR__ . '/fixtures/IT01234567890_11001_reso.xml'],
            'IT01234567890_11002.xml' => [__DIR__ . '/fixtures/IT01234567890_11002.xml'],
            'IT01234567890_FPR02.xml' => [__DIR__ . '/fixtures/IT01234567890_FPR02.xml'],
            'IT01234567899_000sq.xml' => [__DIR__ . '/fixtures/IT01234567899_000sq.xml'],
            'IT00484960588_ERKHK.xml.p7m' => [__DIR__ . '/fixtures/IT00484960588_ERKHK.xml.p7m'],
            'fattura_esempio.xml' => [__DIR__ . '/fixtures/fattura_esempio.xml'],
            'ESEMPIO TD24.xml' => [__DIR__ . '/fixtures/ESEMPIO TD24.xml'],
        ], $privateFileTests);
    }
}
