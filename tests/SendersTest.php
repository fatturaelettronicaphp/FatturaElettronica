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
}
