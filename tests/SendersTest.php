<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use FatturaElettronicaPhp\FatturaElettronica\Address;
use FatturaElettronicaPhp\FatturaElettronica\Customer;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocumentInstance;
use FatturaElettronicaPhp\FatturaElettronica\Enums;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidCaseException;
use FatturaElettronicaPhp\FatturaElettronica\Line;
use FatturaElettronicaPhp\FatturaElettronica\Parser\DigitalDocumentParser;
use FatturaElettronicaPhp\FatturaElettronica\Senders\AcubeSender;
use FatturaElettronicaPhp\FatturaElettronica\Senders\ArubaSender;
use FatturaElettronicaPhp\FatturaElettronica\Supplier;
use FatturaElettronicaPhp\FatturaElettronica\Total;
use FatturaElettronicaPhp\FatturaElettronica\Writer\Header\TransmissionDataWriter;
use PHPUnit\Framework\TestCase;

class SendersTest extends TestCase
{


	public function test_acube_send()
	{
		$file      = __DIR__ . '/fixtures/IT01234567899_000sq.xml';
		$xml       = simplexml_load_file($file);
		$eDocument = DigitalDocument::parseFrom($xml);
		$acube = new AcubeSender('YOUR_USERNAME','YOUR_PASSWORD');

		if($eDocument->isValid()){
			$acube->send($eDocument);
		} else {
			$errors = $eDocument->validate()->errors();
		}
	}
	public function test_aruba_send()
	{
		$file      = __DIR__ . '/fixtures/IT01234567899_000sq.xml';
		$xml       = simplexml_load_file($file);
		$eDocument = DigitalDocument::parseFrom($xml);
		$aruba = new ArubaSender('YOUR_USERNAME','YOUR_PASSWORD');
		$aruba->setDevelopment();
		if($eDocument->isValid()){
			$aruba->send($eDocument);
		} else {
			$errors = $eDocument->validate()->errors();
		}
	}
	public function test_create_invoice()
	{

		$eDocument = new DigitalDocument();
		/**
		 * DatiTrasmissione
		 */
		//progressivo invio
		$eDocument->setSendingId(1);
		$eDocument->setCountryCode('IT');
		$eDocument->setSenderVatId('01879020517');
		$eDocument->setTransmissionFormat(Enums\TransmissionFormat::FPR12()->value);
		$eDocument->setCustomerSdiCode('12345');
		$eDocument->setSenderPhone('33342545454');
		$eDocument->setSenderEmail('aruba@aruba.it');
		$eDocument->setCustomerPec('customer@pec.it');
		/**
		 * END DatiTrasmissione
		 */

		/**
		 * CedentePrestatore
		 */
		$supplier = new Supplier();
		$supplier->setCountryCode('IT');
		$supplier->setVatNumber('01879020517');
		$supplier->setName('Kristian lentino srl');
		$supplier->setTaxRegime(Enums\TaxRegime::RF01()->value);
		/**
		 * Indirizzo cedente prestatore
		 */
		$address = new Address();
		$address->setCountryCode('IT');
		$address->setState('PV');
		$address->setCity('Chignolo Po');
		$address->setStreet('via test');
		$address->setStreetNumber('76');
		$address->setZip('27013');
		$supplier->setAddress($address);
		$eDocument->setSupplier($supplier);
		/**
		 * END CedentePrestatore
		 */

		/**
		 * CessionarioCommittente
		 */
		 $customer = new Customer();
		 $customer->setCountryCode('IT');
		 $customer->setVatNumber('01879020517');
		 $customer->setName('Kristian');
		 $customer->setFiscalCode('LNTKST99P04G388Y');
		 $customer->setSurname('Lentino');
		 $customerAddress = new Address();
		 $customerAddress->setCountryCode('IT');
		 $customerAddress->setState('PV');
		 $customerAddress->setCity('Pavia');
		 $customerAddress->setStreet('via test');
		 $customerAddress->setStreetNumber('76');
		 $customerAddress->setZip('27013');
		 $customer->setAddress($address);
		/**
		 * END CessionarioCommittente
		 */

		$digitalDocumentInstance = new DigitalDocumentInstance();
		$digitalDocumentInstance->setDocumentType(Enums\DocumentType::TD01()->value);
		$digitalDocumentInstance->setCurrency('EUR');
		$digitalDocumentInstance->setDocumentDate(date('Y-m-d'));
		$digitalDocumentInstance->setMainInvoiceNumber('TEST_01');
		$line = new Line();
		$line->setQuantity(1);
		$line->setNumber(1);
		$line->setDescription('Lorem ipsum');
		$line->setUnitPrice(66);
		$line->setTaxPercentage(22);
		$line->setTotal(80.52);
		$line->setTotal(80.52);
		$digitalDocumentInstance->addLine($line);
		$digitalDocumentInstance->setDocumentTotal(80.52);
		$eDocument->addDigitalDocumentInstance($digitalDocumentInstance);


	}
}
